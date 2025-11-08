import json
import boto3
import os
import base64
import zipfile
import tempfile
from urllib.parse import unquote_plus
import requests
import uuid
import time

# Initialize S3 client
s3 = boto3.client('s3')
lambda_client = boto3.client('lambda')

def lambda_handler(event, context):
    """
    AWS Lambda function to convert DOC/DOCX to PDF using LibreOffice
    This function can be triggered by S3 events or API Gateway
    """
    
    # Configuration
    OUTPUT_BUCKET = os.environ.get('OUTPUT_BUCKET', 'your-output-bucket')
    ERROR_BUCKET = os.environ.get('ERROR_BUCKET', 'your-error-bucket')
    CLEANUP_TIMEOUT = int(os.environ.get('CLEANUP_TIMEOUT', '300'))  # 5 minutes
    
    print(f"Starting document conversion process...")
    print(f"Event: {json.dumps(event, indent=2)}")
    
    try:
        # Handle S3 event trigger
        if 'Records' in event:
            return handle_s3_event(event, OUTPUT_BUCKET, ERROR_BUCKET, CLEANUP_TIMEOUT)
        
        # Handle API Gateway trigger
        elif 'httpMethod' in event:
            return handle_api_event(event, OUTPUT_BUCKET, ERROR_BUCKET, CLEANUP_TIMEOUT)
        
        else:
            return {
                'statusCode': 400,
                'body': json.dumps({'error': 'Unsupported event type'})
            }
            
    except Exception as e:
        print(f"Lambda function error: {str(e)}")
        
        # Log error to S3
        error_log = {
            'timestamp': time.time(),
            'error': str(e),
            'event': event,
            'function_name': context.function_name,
            'aws_request_id': context.aws_request_id
        }
        
        try:
            s3.put_object(
                Bucket=ERROR_BUCKET,
                Key=f"errors/{time.time()}-{uuid.uuid4()}.json",
                Body=json.dumps(error_log, indent=2),
                ContentType='application/json'
            )
        except Exception as log_error:
            print(f"Failed to log error: {log_error}")
        
        return {
            'statusCode': 500,
            'body': json.dumps({
                'error': 'Document conversion failed',
                'message': str(e)
            })
        }

def handle_s3_event(event, output_bucket, error_bucket, cleanup_timeout):
    """Handle S3 PUT events for document conversion"""
    
    results = []
    
    for record in event['Records']:
        bucket = record['s3']['bucket']['name']
        key = unquote_plus(record['s3']['object']['key'])
        
        print(f"Processing file: s3://{bucket}/{key}")
        
        # Check if file is a supported format
        if not is_supported_document(key):
            print(f"Skipping unsupported file format: {key}")
            continue
        
        try:
            # Convert document
            result = convert_document_s3(bucket, key, output_bucket, cleanup_timeout)
            results.append(result)
            
        except Exception as e:
            print(f"Failed to convert {key}: {str(e)}")
            results.append({
                'source_key': key,
                'status': 'error',
                'error': str(e)
            })
    
    return {
        'statusCode': 200,
        'body': json.dumps({
            'message': 'Document conversion completed',
            'results': results
        })
    }

def handle_api_event(event, output_bucket, error_bucket, cleanup_timeout):
    """Handle API Gateway events for document conversion"""
    
    try:
        # Parse request body
        body = json.loads(event['body']) if event.get('body') else {}
        
        # Extract parameters
        source_bucket = body.get('source_bucket')
        source_key = body.get('source_key')
        output_key = body.get('output_key')
        
        if not all([source_bucket, source_key]):
            return {
                'statusCode': 400,
                'body': json.dumps({'error': 'Missing required parameters: source_bucket, source_key'})
            }
        
        # Convert document
        result = convert_document_s3(source_bucket, source_key, output_bucket, cleanup_timeout, output_key)
        
        return {
            'statusCode': 200,
            'body': json.dumps(result)
        }
        
    except Exception as e:
        return {
            'statusCode': 500,
            'body': json.dumps({'error': str(e)})
        }

def is_supported_document(key):
    """Check if file is a supported document format"""
    supported_extensions = ['.doc', '.docx', '.odt', '.rtf']
    return any(key.lower().endswith(ext) for ext in supported_extensions)

def convert_document_s3(source_bucket, source_key, output_bucket, cleanup_timeout, output_key=None):
    """Convert document from S3 using LibreOffice in Lambda"""
    
    # Generate unique identifiers
    job_id = str(uuid.uuid4())
    temp_dir = f"/tmp/{job_id}"
    os.makedirs(temp_dir, exist_ok=True)
    
    try:
        print(f"Job {job_id}: Downloading {source_key} from {source_bucket}")
        
        # Download file from S3
        source_path = f"{temp_dir}/{os.path.basename(source_key)}"
        s3.download_file(source_bucket, source_key, source_path)
        
        # Generate output filename
        base_name = os.path.splitext(os.path.basename(source_key))[0]
        output_filename = f"{base_name}.pdf"
        output_path = f"{temp_dir}/{output_filename}"
        
        print(f"Job {job_id}: Converting {source_path} to PDF")
        
        # Install LibreOffice (if not already available)
        install_libreoffice()
        
        # Convert using LibreOffice
        convert_with_libreoffice(source_path, output_path, temp_dir)
        
        # Upload converted file to S3
        final_output_key = output_key or f"converted/{base_name}.pdf"
        
        print(f"Job {job_id}: Uploading to s3://{output_bucket}/{final_output_key}")
        s3.upload_file(output_path, output_bucket, final_output_key)
        
        # Get file metadata
        file_stat = os.stat(output_path)
        
        result = {
            'job_id': job_id,
            'source_bucket': source_bucket,
            'source_key': source_key,
            'output_bucket': output_bucket,
            'output_key': final_output_key,
            'status': 'success',
            'file_size': file_stat.st_size,
            'conversion_time': time.time(),
            's3_url': f"s3://{output_bucket}/{final_output_key}"
        }
        
        print(f"Job {job_id}: Conversion successful - {result['s3_url']}")
        return result
        
    except Exception as e:
        print(f"Job {job_id}: Conversion failed - {str(e)}")
        raise e
    
    finally:
        # Cleanup temporary files
        try:
            if os.path.exists(temp_dir):
                import shutil
                shutil.rmtree(temp_dir)
            print(f"Job {job_id}: Cleaned up temporary files")
        except Exception as cleanup_error:
            print(f"Job {job_id}: Cleanup failed - {cleanup_error}")

def install_libreoffice():
    """Install LibreOffice in Lambda environment"""
    
    libreoffice_path = "/opt/libreoffice"
    
    if os.path.exists(f"{libreoffice_path}/program/soffice"):
        print("LibreOffice already available")
        return
    
    print("Installing LibreOffice...")
    
    # Download LibreOffice binary
    libreoffice_url = "https://download.documentfoundation.org/libreoffice/stable/7.6.3/rpm/x86_64/LibreOffice_7.6.3_Linux_x86-64_rpm.tar.gz"
    
    with tempfile.NamedTemporaryFile(suffix='.tar.gz', delete=False) as temp_file:
        temp_path = temp_file.name
    
    try:
        # Download LibreOffice
        response = requests.get(libreoffice_url)
        response.raise_for_status()
        
        with open(temp_path, 'wb') as f:
            f.write(response.content)
        
        # Extract and install
        import subprocess
        result = subprocess.run([
            'tar', '-xf', temp_path, '-C', '/opt'
        ], capture_output=True, text=True)
        
        if result.returncode != 0:
            raise Exception(f"Failed to extract LibreOffice: {result.stderr}")
        
        # Find the extracted directory and rename
        for item in os.listdir('/opt'):
            if item.startswith('LibreOffice_'):
                old_path = f"/opt/{item}"
                new_path = "/opt/libreoffice"
                if os.path.exists(new_path):
                    import shutil
                    shutil.rmtree(new_path)
                os.rename(old_path, new_path)
                break
        
        print("LibreOffice installed successfully")
        
    finally:
        if os.path.exists(temp_path):
            os.unlink(temp_path)

def convert_with_libreoffice(source_path, output_path, temp_dir):
    """Convert document using LibreOffice"""
    
    import subprocess
    
    libreoffice_path = "/opt/libreoffice"
    soffice_path = f"{libreoffice_path}/program/soffice"
    
    # Set environment variables
    env = os.environ.copy()
    env['HOME'] = '/tmp'
    env['TMPDIR'] = '/tmp'
    
    # LibreOffice conversion command
    cmd = [
        soffice_path,
        '--headless',
        '--convert-to', 'pdf',
        '--outdir', temp_dir,
        source_path,
        '--nologo',
        '--nofirststartwizard'
    ]
    
    print(f"Running command: {' '.join(cmd)}")
    
    result = subprocess.run(
        cmd,
        env=env,
        capture_output=True,
        text=True,
        timeout=300  # 5 minute timeout
    )
    
    print(f"LibreOffice output: {result.stdout}")
    if result.stderr:
        print(f"LibreOffice errors: {result.stderr}")
    
    if result.returncode != 0:
        raise Exception(f"LibreOffice conversion failed: {result.stderr}")
    
    # Check if output file was created
    expected_output = f"{temp_dir}/{os.path.splitext(os.path.basename(source_path))[0]}.pdf"
    if not os.path.exists(expected_output):
        raise Exception("LibreOffice did not create output file")
    
    # Rename to desired output name
    os.rename(expected_output, output_path)
    
    print(f"Conversion completed: {output_path}")

# Additional utility functions

def get_conversion_status(job_id):
    """Get status of a conversion job"""
    # This would typically query a database or DynamoDB table
    # For now, return a placeholder
    return {
        'job_id': job_id,
        'status': 'completed',
        'output_url': f"s3://output-bucket/converted/document-{job_id}.pdf"
    }

def list_conversion_jobs(limit=50):
    """List recent conversion jobs"""
    # This would typically query a database or DynamoDB table
    return {
        'jobs': [],
        'total': 0
    }