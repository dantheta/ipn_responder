<?php


class MissingKeyException extends Exception {};

function load_template($filename) {
    $tmpl = file_get_contents($filename);

    return $tmpl;

}

function substitute_values($tmpl, $vars, $html) {
    return preg_replace_callback(
        '/{([A-Z_]+)}/', 
        function($matches) use ($html,$vars) {
            if (!array_key_exists($matches[1], $vars)) {
                throw new MissingKeyException("Key {$matches[1]} not found");
            }
            if ($html) {
                return htmlentities($vars[$matches[1]]);
            } else {
                return $vars[$matches[1]];
            }
        },
        $tmpl);
}

function run_template($filename, $vars, $html) {
    # very simple templating function

    $tmpl = load_template($filename);
    return substitute_values($tmpl, $vars, $html);

}
