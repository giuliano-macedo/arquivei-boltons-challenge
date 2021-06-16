<?php
class Parser
{
    public static function parseNfe(String $doc): String
    {
        /*
            Parses NFe base64 xml and returns it's total value.

            @param String $doc Base 64 XML Nfe string.
            @returns String NFe value.
        */
        $xml = simplexml_load_string(base64_decode($doc));

        // Set xml namespace so that xpath works
        $ns = $xml->getDocNamespaces();
        $xml->registerXPathNamespace('ns', array_values($ns)[0]);

        $ans = $xml->xpath("//ns:vNF");
        if (!$ans) {
            throw new ErrorException("Couldn't find vNF tag");
        }

        return $ans[0];
    }
}
