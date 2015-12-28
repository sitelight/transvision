<?php
namespace Transvision;

$repositories = ($request->parameters[2] == 'global')
    ? Project::getRepositories()
    : [$request->parameters[2]];

$source_strings_merged = [];
$target_strings_merged = [];

// The search
$initial_search = Utils::cleanString($request->parameters[5]);
$terms = Utils::uniqueWords($initial_search);

// Regex options (not currenty used)
$delimiter = '~';
$whole_word = isset($check['whole_word']) ? '\b' : '';
$case_sensitive = isset($check['case_sensitive']) ? '' : 'i';
$regex = $delimiter . $whole_word . $initial_search . $whole_word .
         $delimiter . $case_sensitive . 'u';

 // Loop through all repositories searching in both source and target languages
foreach ($repositories as $repository) {
    $source_strings = Utils::getRepoStrings($request->parameters[3], $repository);
    foreach ($terms as $word) {
        $regex = $delimiter . $whole_word . preg_quote($word, $delimiter) .
                 $whole_word . $delimiter . $case_sensitive . 'u';
        $source_strings = preg_grep($regex, $source_strings);
    }
    $source_strings_merged = array_merge($source_strings, $source_strings_merged);

    $target_strings = Utils::getRepoStrings($request->parameters[4], $repository);
    foreach ($terms as $word) {
        $regex = $delimiter . $whole_word . preg_quote($word, $delimiter) .
                 $whole_word . $delimiter . $case_sensitive . 'u';
        $target_strings = preg_grep($regex, $target_strings);
    }
    $target_strings_merged = array_merge($target_strings, $target_strings_merged);
}

// Closure to get extra parameters set
$get_option = function ($option) use ($request) {
    $value = 0;
    if (isset($request->extra_parameters[$option])
        && (int) $request->extra_parameters[$option] != 0) {
        $value = (int) $request->extra_parameters[$option];
    }

    return $value;
};

return $json = ShowResults::getSuggestionsResults(
    $source_strings_merged,
    $target_strings_merged,
    $initial_search,
    $get_option('max_results')
);