<?php

declare(strict_types=1);

namespace Sg3w\Inlinefluid\DataProvider;

class XmlDataProvider implements DataProviderInterface
{
    public function renderData(string $dataString): mixed
    {
        $previous = libxml_use_internal_errors(true);
        $xmlObject = simplexml_load_string($dataString, \SimpleXMLElement::class, LIBXML_NOCDATA);
        $errors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if ($xmlObject === false) {
            $message = $errors !== [] ? trim($errors[0]->message) : 'unknown error';
            throw new \RuntimeException('The given data is no valid XML: ' . $message, 1760000011);
        }

        return json_decode((string)json_encode($xmlObject), true);
    }
}
