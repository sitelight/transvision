<?php
namespace Transvision;

// Include the common simple search form
include __DIR__ . '/simplesearchform.php';

if (! empty($ak_results)) {
    $search_id = 'accesskeys';
    $content = '';
    if (! empty($error_messages)) {
        $content .= '<p class="error">' .
            implode('<br/>', $error_messages) .
            '</p>';
    }
    $content .= "<h2><span class=\"results_count_{$search_id}\">"
        . Utils::pluralize(count($ak_results), 'potential access key error')
        . "</span> found</h2>\n";

    if (isset($filter_block)) {
        $content .= $filter_block;
    }

    $content .= "
        <table class='collapsable results_table sortable {$search_id}'>
          <thead>
            <tr class='column_headers'>
              <th>Entity</th>
              <th>Label</th>
              <th>Access&nbsp;key</th>
              <th>Access&nbsp;key entity</th>
            </tr>
          </thead>
          <tbody>\n";

    // Get the tool used to edit strings for the target locale
    $toolUsedByTargetLocale = Project::getLocaleTool($locale);

    foreach ($ak_results as $ak_string => $ak_label) {
        // Link to entity
        $ak_link = "?sourcelocale={$reference_locale}" .
           "&locale={$locale}" .
           "&repo={$repo}" .
           "&search_type=entities&recherche={$ak_string}" .
           '&entire_string=entire_string';
        $label_link = "?sourcelocale={$reference_locale}" .
           "&locale={$locale}" .
           "&repo={$repo}" .
           "&search_type=entities&recherche={$ak_label}" .
           '&entire_string=entire_string';

        $path_ak = VersionControl::hgPath($locale, $repo, $ak_string);
        $path_label = VersionControl::hgPath($locale, $repo, $ak_label);

        $edit_link_ak = $toolUsedByTargetLocale != ''
            ? ShowResults::getEditLink($toolUsedByTargetLocale, $repo, $ak_string, $target[$ak_string], $locale)
            : '';
        $edit_link_label = $toolUsedByTargetLocale != ''
            ? ShowResults::getEditLink($toolUsedByTargetLocale, $repo, $ak_label, $target[$ak_label], $locale)
            : '';

        $ak_value = ! empty($target[$ak_string])
            ? Utils::secureText($target[$ak_string])
            : '<em class="error">(empty)</em>';
        $label_value = ! empty($target[$ak_label])
            ? Utils::secureText($target[$ak_label])
            : '<em class="error">(empty)</em>';

        $component = explode('/', $ak_string)[0];
        $content .= "<tr class='{$component} {$search_id}'>
                       <td>
                          <span class='celltitle'>Entity</span>
                          <span class='link_to_entity'>
                            <a href=\"/{$label_link}\">" . ShowResults::formatEntity($ak_label) . "</a>
                          </span>
                       </td>
                       <td dir='{$direction}'>
                          <span class='celltitle'>Label</span>
                          <div class='string'>{$label_value}</div>
                          <div dir='ltr' class='result_meta_link'>
                            <a class='source_link' href='{$path_label}'>&lt;source&gt;</a>
                            {$edit_link_label}
                          </div>
                       </td>
                       <td dir='{$direction}'>
                          <span class='celltitle'>Access&nbsp;key</span>
                          <div class='string'>{$ak_value}</div>
                          <div dir='ltr' class='result_meta_link'>
                            <a class='source_link' href='{$path_ak}'>&lt;source&gt;</a>
                            {$edit_link_ak}
                          </div>
                       </td>
                       <td>
                          <span class='celltitle'>Access&nbsp;key entity</span>
                          <span class='link_to_entity'>
                            <a href=\"/{$ak_link}\">" . ShowResults::formatEntity($ak_string) . "</a>
                          </span>
                       </td>
                     </tr>\n";
    }
    $content .= "</tbody>\n</table>\n";
} else {
    $content = '<h2>Congratulations, no errors found.</h2>';
}

print $content;
