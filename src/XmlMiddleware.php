<?php
namespace GuzzleXml;

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;

class XmlMiddleware
{
    const HEADER_CONTENT_TYPE = 'Content-Type';
    const MIME_TYPE_XML = 'text/xml';
    const OPTION_XML = 'xml';

    public static function xml(array $context = ['xml_encoding' => 'utf-8'])
    {
        return function (callable $handler) use ($context) {
            return function (RequestInterface $request, array $options) use ($handler, $context) {
                if (!isset($options[self::OPTION_XML])) {
                    return $handler($request, $options);
                }

                if (is_array($options[self::OPTION_XML]) && !array_is_list($options[self::OPTION_XML])) {
                    $encoder = new XmlEncoder([
                        XmlEncoder::ROOT_NODE_NAME => key($options[self::OPTION_XML])
                    ]);
                    $data = current($options[self::OPTION_XML]);
                } else {
                    $encoder = new XmlEncoder();
                    $data = $options[self::OPTION_XML];
                }

                $body = $encoder->encode($data, XmlEncoder::FORMAT, $context);
                unset($options[self::OPTION_XML]);
                $request = $request->withHeader(self::HEADER_CONTENT_TYPE, self::MIME_TYPE_XML)
                    ->withBody(Utils::streamFor($body));

                return $handler($request, $options);
            };
        };
    }

    public static function jsonToXml()
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $promise = $handler($request, $options);
                return $promise->then(
                    function (ResponseInterface $response) {
                        $json = (string) $response->getBody();
                        if (!$json) {
                            return $response;
                        }

                        $encoder = new JsonEncoder();
                        $schema = $encoder->decode($json, JsonEncoder::FORMAT);

                        if (!array_is_list($schema)) {
                            $encoder = new XmlEncoder([
                                XmlEncoder::ROOT_NODE_NAME => key($schema)
                            ]);
                            $data = current($schema);
                        } else {
                            $encoder = new XmlEncoder();
                            $data = $schema;
                        }
                        
                        $body = $encoder->encode($data, XmlEncoder::FORMAT);
                        return $response->withHeader(self::HEADER_CONTENT_TYPE, self::MIME_TYPE_XML)
                            ->withBody(Utils::streamFor($body));
                    }
                );
            };
        };
    }
}