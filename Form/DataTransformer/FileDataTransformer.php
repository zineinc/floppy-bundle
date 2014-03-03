<?php


namespace ZineInc\StorageBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use ZineInc\Storage\Common\FileId;

class FileDataTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if($value instanceof FileId) {
            $value = $value->id();
        }

        if($value === null) {
            return null;
        }

        if(!is_string($value)) {
            throw new TransformationFailedException('Cannot transform value, unexpected type, expected "ZineInc\Storage\Common\FileId", but '.self::type($value).' given');
        }

        return $value;
    }

    private static function type($value)
    {
        return is_object($value) ? get_class($value) : gettype($value);
    }

    public function reverseTransform($value)
    {
        if($value === null) {
            return null;
        }

        if(is_array($value) && array_key_exists('id', $value) && ($value['id'] === null || is_string($value['id']))) {
            $id = $value['id'];
            $serializedAttrs = isset($value['attributes']) ? (string) $value['attributes'] : null;
            $attrs = $serializedAttrs ? @json_decode($serializedAttrs, true) : array();

            if($attrs === null) {
                throw new TransformationFailedException('Cannot transform value, invalid attributes value, expected valid json format, but "'.$serializedAttrs.'" given');
            }

            return new FileId($id, $attrs);
        } elseif(is_string($value)) {
            return new FileId($value);
        }

        throw new TransformationFailedException('Cannot transform value, unexpected value, expected string or array with "id" key, but '.self::type($value).' given');
    }
}