<?php
// Set the Content-Type header for JSON response
header('Content-Type: application/json');

// Azure Blob Storage connection settings
$connectionString = "DefaultEndpointsProtocol=https;AccountName=imageappstoragee;AccountKey=6pFvb5Wowbuc1kung7FAzJ4cce3y/7m+8L9EBLzLkqMzU/GkkEMd6XyOWfv05BrCwNF9GKrcgXih+ASt5K5wMA==;EndpointSuffix=core.windows.net"; // Replace with your Azure Storage connection string
$containerName = "images";        // Replace with your container name

// Import the required Azure SDK for PHP
require 'vendor/autoload.php'; // Assuming you are using Composer to manage dependencies

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

try {
    // Create a blob client
    $blobClient = BlobRestProxy::createBlobService($connectionString);
    
    // List blobs in the container
    $blobList = $blobClient->listBlobs($containerName);
    
    $images = [];
    
    foreach ($blobList->getBlobs() as $blob) {
        // Check for image file types (e.g., jpg, jpeg, png, gif)
        $fileName = $blob->getName();
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($fileExtension), ['jpg', 'jpeg', 'png', 'gif'])) {
            // Generate the blob URL (use SAS token if needed for security)
            $blobUrl = $blobClient->getBlobUrl($containerName, $fileName);
            
            // Add the image URL and name to the array
            $images[] = [
                'url' => $blobUrl,
                'name' => $fileName
            ];
        }
    }

    // Return the images as a JSON response
    echo json_encode($images);

} catch (ServiceException $e) {
    // Handle exceptions
    $errorMessage = $e->getMessage();
    echo json_encode(['error' => $errorMessage]);
}
