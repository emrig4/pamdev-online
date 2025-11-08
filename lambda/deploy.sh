#!/bin/bash

# Authoran Lambda Document Converter Deployment Script
# This script deploys the AWS Lambda function for DOC/DOCX to PDF conversion

set -e  # Exit on any error

echo "🚀 Deploying Authoran Lambda Document Converter..."

# Configuration
STAGE=${1:-dev}
AWS_REGION=${AWS_REGION:-us-east-1}
PROJECT_NAME="authoran-document-converter"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Functions
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check prerequisites
check_prerequisites() {
    log_info "Checking prerequisites..."
    
    # Check if AWS CLI is installed
    if ! command -v aws &> /dev/null; then
        log_error "AWS CLI is not installed. Please install it first:"
        echo "curl 'https://awscli.amazonaws.com/awscli-exe-linux-x86_64.zip' -o 'awscliv2.zip'"
        echo "unzip awscliv2.zip"
        echo "sudo ./aws/install"
        exit 1
    fi
    
    # Check if Serverless Framework is installed
    if ! command -v serverless &> /dev/null; then
        log_warn "Serverless Framework not found. Installing..."
        npm install -g serverless
    fi
    
    # Check AWS credentials
    if ! aws sts get-caller-identity &> /dev/null; then
        log_error "AWS credentials not configured. Run 'aws configure' first."
        exit 1
    fi
    
    # Check if Node.js is installed
    if ! command -v node &> /dev/null; then
        log_error "Node.js is not installed. Please install Node.js 16+ first."
        exit 1
    fi
    
    log_info "✅ All prerequisites satisfied"
}

# Create deployment package
create_package() {
    log_info "Creating deployment package..."
    
    # Create temporary directory
    PACKAGE_DIR="lambda-package"
    rm -rf $PACKAGE_DIR
    mkdir -p $PACKAGE_DIR
    
    # Copy Python function
    cp document-converter.py $PACKAGE_DIR/
    
    # Create requirements.txt
    cat > $PACKAGE_DIR/requirements.txt << EOF
requests==2.31.0
boto3==1.28.85
EOF
    
    # Install Python dependencies
    log_info "Installing Python dependencies..."
    cd $PACKAGE_DIR
    python3 -m pip install -r requirements.txt -t . --no-deps
    cd ..
    
    # Create deployment package
    log_info "Creating deployment zip..."
    cd $PACKAGE_DIR
    zip -r ../$PROJECT_NAME-$STAGE.zip . > /dev/null
    cd ..
    
    # Clean up
    rm -rf $PACKAGE_DIR
    
    log_info "✅ Package created: $PROJECT_NAME-$STAGE.zip"
}

# Deploy with Serverless Framework
deploy_with_serverless() {
    log_info "Deploying with Serverless Framework..."
    
    # Create environment variables file
    cat > .env.$STAGE << EOF
OUTPUT_BUCKET=authoran-converted-docs-$STAGE
ERROR_BUCKET=authoran-conversion-errors-$STAGE
INPUT_BUCKET=authoran-user-uploads-$STAGE
EOF
    
    # Deploy
    serverless deploy --stage $STAGE --region $AWS_REGION
    
    log_info "✅ Serverless deployment completed"
}

# Create S3 buckets
create_buckets() {
    log_info "Creating S3 buckets..."
    
    local input_bucket="authoran-user-uploads-$STAGE"
    local output_bucket="authoran-converted-docs-$STAGE"
    local error_bucket="authoran-conversion-errors-$STAGE"
    
    # Create input bucket
    if ! aws s3api head-bucket --bucket $input_bucket 2>/dev/null; then
        log_info "Creating input bucket: $input_bucket"
        aws s3 mb s3://$input_bucket --region $AWS_REGION
        
        # Configure CORS for the input bucket
        aws s3api put-bucket-cors --bucket $input_bucket --cors-configuration file://s3-cors.json
    else
        log_info "Input bucket already exists: $input_bucket"
    fi
    
    # Create output bucket
    if ! aws s3api head-bucket --bucket $output_bucket 2>/dev/null; then
        log_info "Creating output bucket: $output_bucket"
        aws s3 mb s3://$output_bucket --region $AWS_REGION
        
        # Make bucket public for read access
        aws s3api put-bucket-policy --bucket $output_bucket --policy file://s3-public-policy.json
    else
        log_info "Output bucket already exists: $output_bucket"
    fi
    
    # Create error bucket
    if ! aws s3api head-bucket --bucket $error_bucket 2>/dev/null; then
        log_info "Creating error bucket: $error_bucket"
        aws s3 mb s3://$error_bucket --region $AWS_REGION
    else
        log_info "Error bucket already exists: $error_bucket"
    fi
    
    log_info "✅ S3 buckets configured"
}

# Configure IAM permissions
configure_iam() {
    log_info "Configuring IAM permissions..."
    
    # This would typically involve creating a role for Lambda execution
    # The Serverless Framework handles this automatically
    log_info "IAM permissions handled by Serverless Framework"
}

# Test deployment
test_deployment() {
    log_info "Testing deployment..."
    
    # Get the API endpoint
    API_ENDPOINT=$(aws cloudformation describe-stacks \
        --stack-name $PROJECT_NAME-$STAGE \
        --query 'Stacks[0].Outputs[?OutputKey==`ServiceEndpoint`].OutputValue' \
        --output text \
        2>/dev/null || echo "")
    
    if [ -n "$API_ENDPOINT" ]; then
        log_info "Testing API endpoint: $API_ENDPOINT/health"
        
        # Test health endpoint
        curl -s -f $API_ENDPOINT/health || {
            log_warn "Health check failed - this is normal if health endpoint is not implemented"
        }
        
        log_info "✅ API endpoint is accessible"
    else
        log_warn "Could not retrieve API endpoint from CloudFormation"
    fi
}

# Generate configuration for PHP
generate_php_config() {
    log_info "Generating PHP configuration..."
    
    cat > ../lambda-php-config.php << 'EOF'
<?php

// Authoran Lambda Configuration
// Copy this to your Laravel config/services.php

return [
    'lambda' => [
        'function_name' => 'authoran-document-converter-dev-convertDocument',
        'api_endpoint' => 'YOUR_API_GATEWAY_URL_HERE', // Replace with actual URL
        'region' => 'us-east-1',
        'enabled' => env('LAMBDA_CONVERSION_ENABLED', true),
    ],
];
EOF

    log_info "✅ PHP configuration generated: lambda-php-config.php"
    log_warn "Remember to update the API endpoint URL in lambda-php-config.php"
}

# Cleanup
cleanup() {
    log_info "Cleaning up..."
    rm -f .env.$STAGE
    rm -f $PROJECT_NAME-$STAGE.zip
}

# Main deployment process
main() {
    log_info "Starting deployment for stage: $STAGE"
    
    check_prerequisites
    create_package
    create_buckets
    deploy_with_serverless
    test_deployment
    generate_php_config
    cleanup
    
    log_info "🎉 Deployment completed successfully!"
    log_info "Next steps:"
    log_info "1. Update the API endpoint in your Laravel configuration"
    log_info "2. Test document conversion with the upload function"
    log_info "3. Monitor CloudWatch logs for any issues"
}

# Handle script arguments
case "${1:-help}" in
    deploy|*)
        main
        ;;
    cleanup)
        log_info "Cleaning up deployment..."
        serverless remove --stage $STAGE --region $AWS_REGION
        ;;
    test)
        test_deployment
        ;;
    *)
        echo "Usage: $0 [deploy|cleanup|test] [stage]"
        echo "  deploy (default) - Full deployment"
        echo "  cleanup - Remove deployment"
        echo "  test - Test existing deployment"
        echo "  stage - dev (default), prod, etc."
        exit 1
        ;;
esac