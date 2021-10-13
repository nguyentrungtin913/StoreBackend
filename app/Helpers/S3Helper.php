<?php


namespace App\Helpers;


use Aws\Result;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
class S3Helper
{
    /**
     * @return S3Client
     */
    static function get()
    {
        return new S3Client([
//                'profile' => 'default',
            'version' => 'latest',
            'region' => getenv('S3_REGION'),
            'credentials' => [
                'key' => getenv('S3_KEY'),
                'secret' => getenv('S3_SECRET')
            ]
        ]);
    }

    /**
     * @param $key
     * @param $body
     * @return mixed
     */
    static function upload($key, $body)
    {
        return static::get()->upload(getenv('S3_BUCKET'), getenv('S3_DIR') . $key, $body, 'public-read');
    }

    /**
     * @param $key
     * @param $body
     * @return Result
     */
    static function putObject($key, $body)
    {
        $params = ['Bucket' => getenv('S3_BUCKET'), 'Key' => getenv('S3_DIR') . $key, 'Body' => $body, 'ACL' => 'public-read'];
        return static::get()->putObject($params);
    }

    /**
     * @param $filePath
     * @return mixed
     */
    static function uploadByFilePath($filePath)
    {
        return static::upload($filePath, fopen(ImageHelper::getPathUpload() . $filePath));
    }

    /**
     * @param $filePath
     * @return Result
     */
    static function putObjectByFilePath($filePath)
    {
        return static::putObject($filePath, fopen(ImageHelper::getPathUpload() . $filePath, 'r'));
    }

    /**
     * @param $key
     * @return string
     */
    static function getObjectUrl($key)
    {
        return static::get()->getObjectUrl(getenv('S3_BUCKET'), getenv('S3_DIR') . $key);
    }

    /**
     * @param string $key
     * @param string $contentType
     * @return string
     */
    static function getPreSignedUrl($key = 'testkey', $contentType = '')
    {
        $params = [
            'Bucket' => getenv('S3_BUCKET'),
            'Key' => getenv('S3_DIR') . $key,
            'ACL' => 'public-read'
        ];
        if(!empty($contentType)) $params['ContentType'] = $contentType;
        $cmd = static::get()->getCommand('PutObject', $params);
        $request = static::get()->createPresignedRequest($cmd, '+2 minutes');
        return (string)$request->getUri();
    }

    /**
     * @param string $key
     * @param string $contentType
     * @return string
     */
    static function getPreSignedUrlByAccessPoint($key = 'testkey', $contentType = '')
    {
        $params = [
            'Bucket' => "arn:aws:s3:".getenv('S3_REGION').":".getenv('S3_ACCOUNT_ID').":accesspoint:". getenv('S3_ACCESS_POINT_NAME'),
            'Key' => getenv('S3_DIR') . $key,
            'ACL' => 'public-read'
        ];
        if(!empty($contentType)) $params['ContentType'] = $contentType;
        $cmd = static::get()->getCommand('PutObject', $params);
        $request = static::get()->createPresignedRequest($cmd, '+2 minutes');
        return (string)$request->getUri();
    }
}