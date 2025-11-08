# AWS Lambda Document Converter for Authoran

## Overview

This Lambda-based solution provides serverless document conversion for your Authoran platform, automatically converting DOC/DOCX files to PDF using AWS Lambda functions.

## Architecture

```
User Upload → S3 Input Bucket → Lambda Function → S3 Output Bucket → Laravel App
                      ↓
              CloudWatch Logs
                      ↓
              API Gateway → PHP Integration
```

## Key Benefits

### 🚀 **Scalability**
- **Serverless**: No server management required
- **Auto-scaling**: Handles traffic spikes automatically
- **Cost-effective**: Pay only for actual usage

### ⚡ **Performance**
- **Fast conversion**: Optimized for document processing
- **Global deployment**: Works with multiple AWS regions
- **Low latency**: S3-triggered conversions

### 🛡️ **Reliability**
- **High availability**: 99.9% uptime SLA
- **Error handling**: Comprehensive error logging
- **Monitoring**: CloudWatch integration

## Quick Start

### 1. Prerequisites

```bash
# Install AWS CLI
curl "https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip" -o "awscliv2.zip"
unzip awscliv2.zip
sudo ./aws/install

# Install Serverless Framework
npm install -g serverless

# Configure AWS credentials
aws configure
```

### 2. Deploy Lambda Function

```bash
# Make deployment script executable
chmod +x deploy.sh

# Deploy for development
./deploy.sh deploy dev

# Deploy for production
./deploy.sh deploy prod
```

### 3. Configure Laravel

```php
// Add to config/services.php
return [
    'lambda' => [
        'function_name' => 'authoran-document-converter-dev-convertDocument',
        'api_endpoint' => 'https://your-api.execute-api.us-east-1.amazonaws.com',
        'region' => 'us-east-1',
        'enabled' => env('LAMBDA_CONVERSION_ENABLED', true),
    ],
];
```

### 4. Test the System

```bash
# Check deployment status
./deploy.sh test

# Upload a test document via your Laravel app
```

## File Structure

```
lambda/
├── document-converter.py      # Main Lambda function
├── serverless.yml             # Serverless configuration
├── deploy.sh                  # Deployment script
├── s3-cors.json              # S3 CORS configuration
├── s3-public-policy.json     # S3 public access policy
├── README.md                 # This file
└── lambda-php-config.php     # Generated PHP config
```

## How It Works

### 1. **Document Upload**
```php
// Laravel uploads file to S3
$converter = new LambdaDocumentConverter();
$result = $converter->uploadToS3ForConversion($file, $filename);
```

### 2. **Automatic Conversion**
- S3 triggers Lambda function on file upload
- Lambda downloads document from S3
- LibreOffice converts DOC/DOCX to PDF
- Converted file uploaded back to S3

### 3. **Laravel Integration**
```php
// Check conversion status
$status = $converter->getConversionStatus($jobId);

// Wait for conversion completion
$result = $converter->waitForConversion($jobId);

// Process converted document
$tempFile = $converter->processConvertedDocument($result);
```

## Configuration Options

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `OUTPUT_BUCKET` | S3 bucket for converted PDFs | `authoran-converted-docs-dev` |
| `ERROR_BUCKET` | S3 bucket for error logs | `authoran-conversion-errors-dev` |
| `INPUT_BUCKET` | S3 bucket for user uploads | `authoran-user-uploads-dev` |
| `CLEANUP_TIMEOUT` | Temporary file cleanup timeout | `300` (seconds) |

### Lambda Settings

| Setting | Value | Notes |
|---------|-------|-------|
| Runtime | Python 3.9 | Latest supported version |
| Memory | 3008 MB | Optimized for document processing |
| Timeout | 900 seconds | 15 minutes for large documents |
| Concurrency | 1000 | Auto-scaling enabled |

## Monitoring and Logs

### CloudWatch Integration
- **Function logs**: Automatic logging to CloudWatch
- **Error tracking**: Failed conversions logged to S3
- **Performance metrics**: Conversion time tracking
- **Custom metrics**: Success/failure rates

### Log Analysis
```bash
# View Lambda function logs
aws logs tail /aws/lambda/authoran-document-converter-dev-convertDocument --follow

# Filter for errors
aws logs filter-log-events --log-group-name /aws/lambda/authoran-document-converter-dev-convertDocument --filter-pattern "ERROR"
```

## Security

### IAM Permissions
- **Minimal permissions**: Only access required S3 buckets
- **No public access**: Private Lambda function
- **Encrypted storage**: S3 bucket encryption enabled
- **Signed URLs**: Secure file access

### Data Protection
- **In-transit encryption**: HTTPS/TLS
- **At-rest encryption**: S3 server-side encryption
- **Access logging**: CloudTrail integration
- **Data retention**: Configurable cleanup policies

## Troubleshooting

### Common Issues

#### 1. **Conversion Timeout**
```bash
# Check function logs
aws logs filter-log-group --log-group-name /aws/lambda/authoran-document-converter-dev-convertDocument --filter-pattern "timeout"

# Increase Lambda timeout in serverless.yml
timeout: 1800  # 30 minutes
```

#### 2. **Permission Errors**
```bash
# Check IAM role permissions
aws iam get-role --role-name authoran-document-converter-dev-us-east-1-lambdaRole

# Verify S3 bucket policies
aws s3api get-bucket-policy --bucket your-bucket-name
```

#### 3. **Function Not Triggering**
```bash
# Check S3 event configuration
aws s3api get-bucket-notification-configuration --bucket your-input-bucket

# Verify CORS settings
aws s3api get-bucket-cors --bucket your-input-bucket
```

### Performance Optimization

#### 1. **Cold Start Issues**
- Use provisioned concurrency for production
- Monitor cold start times in CloudWatch
- Consider increasing memory allocation

#### 2. **Large File Handling**
- Increase Lambda timeout for large documents
- Enable multi-part uploads for files > 100MB
- Consider S3 multipart upload optimization

## Cost Optimization

### Pricing Model
- **Lambda**: $0.0000166667 per GB-second
- **S3**: $0.023/GB for storage, $0.09/GB for transfers
- **API Gateway**: $3.50 per million requests
- **CloudWatch**: $0.30 per GB of logs

### Cost Estimation
- **Small documents** (1-5 pages): ~$0.001 per conversion
- **Medium documents** (10-50 pages): ~$0.005 per conversion
- **Large documents** (100+ pages): ~$0.015 per conversion

### Optimization Tips
- Enable S3 lifecycle policies for automatic cleanup
- Use CloudWatch log retention policies
- Monitor function execution time
- Implement retry logic with exponential backoff

## Migration from Server Installation

### From LibreOffice on Server
1. **Deploy Lambda function**
2. **Update Laravel configuration**
3. **Test with sample documents**
4. **Gradually migrate traffic**
5. **Remove server installation**

### Zero Downtime Migration
```php
// Hybrid approach during transition
$useLambda = config('services.lambda.enabled', false);

if ($useLambda) {
    // Use Lambda conversion
    $result = $lambdaConverter->convertDocument(...);
} else {
    // Use existing server conversion
    $result = $serverConverter->convertDocument(...);
}
```

## Support and Maintenance

### Regular Tasks
- **Monitor CloudWatch metrics** weekly
- **Review error logs** monthly  
- **Update Lambda runtime** quarterly
- **Optimize costs** quarterly

### Backup and Recovery
- **S3 versioning**: Enable for critical buckets
- **Lambda versions**: Automatic with Serverless Framework
- **Configuration backup**: Version control for serverless.yml

## Advanced Features

### Webhook Notifications
```python
# Add to Lambda function for completion notifications
import requests

def notify_webhook(conversion_result):
    webhook_url = os.environ.get('WEBHOOK_URL')
    if webhook_url:
        requests.post(webhook_url, json={
            'event': 'document_converted',
            'data': conversion_result
        })
```

### Batch Processing
```python
# Process multiple documents in single Lambda invocation
def batch_convert_documents(source_keys):
    # Convert multiple documents efficiently
    # Reduce Lambda invocations and costs
    pass
```

### Custom Processing
```python
# Add watermarks, merge documents, etc.
def add_watermark_to_pdf(pdf_path, watermark_text):
    # Integrate with PDFtk or similar tools
    pass
```

---

## Conclusion

AWS Lambda provides a superior, scalable solution for document conversion compared to server installations. This implementation offers:

- ✅ **Zero server management**
- ✅ **Automatic scaling**
- ✅ **Cost optimization**
- ✅ **High availability**
- ✅ **Easy maintenance**

**Ready to upgrade your document conversion system?** Run the deployment script and experience the power of serverless document processing! 🚀

*For technical support, check the CloudWatch logs and AWS documentation.*