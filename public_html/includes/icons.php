<?php
/**
 * includes/icons.php
 * Small inline-SVG icon set for the parent/teacher portals — 24x24,
 * stroke-based, currentColor (same convention as the notification bell
 * in templates/notification-bell.php, so new icons match its weight).
 *
 * Deliberately NOT an icon font/CDN: portal nav renders on every page
 * for a small business's paying customers, so it shouldn't depend on a
 * third-party CDN being up. Inline SVG has zero runtime dependency,
 * recolors with plain CSS `color`, and is a few hundred bytes each.
 *
 * Replaces the bare emoji that were previously used for primary
 * navigation and controls across the Parent Portal, Teacher Portal,
 * Hub (staff ops tool), and the public School section — kept emoji
 * elsewhere (empty states, celebratory copy, WhatsApp CTAs) where they
 * read as warm rather than unfinished.
 */

if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === 'icons.php') {
    http_response_code(403);
    exit('Direct access denied.');
}

function ttIcon(string $name, string $class = 'tt-icon', int $size = 20): string
{
    $paths = [
        'home'       => '<path d="M4 10 12 3l8 7"/><path d="M6 9v11h4v-6h4v6h4V9"/>',
        'calendar'   => '<rect x="3" y="4.5" width="18" height="16" rx="2" ry="2"/><line x1="16" y1="2.5" x2="16" y2="6.5"/><line x1="8" y1="2.5" x2="8" y2="6.5"/><line x1="3" y1="9.5" x2="21" y2="9.5"/>',
        'message'    => '<rect x="2.5" y="4" width="19" height="13" rx="3" ry="3"/><polygon points="7,17 7,21 11,17"/>',
        'card'       => '<rect x="1.5" y="5" width="21" height="14" rx="2" ry="2"/><line x1="1.5" y1="10" x2="22.5" y2="10"/>',
        'sliders'    => '<line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>',
        'logout'     => '<polyline points="9,21 5,21 5,3 9,3"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/>',
        'bell'       => '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
        'arrow-left' => '<line x1="19" y1="12" x2="5" y2="12"/><polyline points="12,19 5,12 12,5"/>',
        'check'      => '<circle cx="12" cy="12" r="9.5"/><polyline points="7.5,12.5 10.5,15.5 16.5,8.5"/>',
        'camera'     => '<polygon points="3,7 8,7 9.5,4.5 14.5,4.5 16,7 21,7 21,19 3,19"/><circle cx="12" cy="13" r="3.5"/>',
        'smile'      => '<circle cx="12" cy="12" r="9.5"/><polyline points="7.5,13.5 9.5,15.5 12,16 14.5,15.5 16.5,13.5"/><line x1="8.7" y1="9.3" x2="8.71" y2="9.3"/><line x1="15.3" y1="9.3" x2="15.31" y2="9.3"/>',
        'star'       => '<polygon points="12,2.5 15,8.9 22,9.8 17,14.7 18.2,21.6 12,18.3 5.8,21.6 7,14.7 2,9.8 9,8.9"/>',
        'smartphone' => '<rect x="6.5" y="1.5" width="11" height="21" rx="1.5" ry="1.5"/><line x1="10.5" y1="18.5" x2="13.5" y2="18.5"/>',
        'plus'       => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>',
        'tent'       => '<polygon points="12,3 21,19 15,19 12,13 9,19 3,19"/><line x1="12" y1="13" x2="12" y2="19"/>',
        'book'       => '<rect x="4" y="3" width="16" height="18" rx="1.5" ry="1.5"/><line x1="8" y1="8" x2="16" y2="8"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="8" y1="16" x2="13" y2="16"/>',
        'award'      => '<circle cx="12" cy="8.5" r="5.5"/><polyline points="8.5,13.5 7,21.5 12,18.5 17,21.5 15.5,13.5"/>',
        'flask'      => '<line x1="9" y1="2" x2="9" y2="8.5"/><line x1="15" y1="2" x2="15" y2="8.5"/><line x1="9" y1="2" x2="15" y2="2"/><polygon points="9,8.5 15,8.5 20,21 4,21"/><line x1="6.2" y1="15.5" x2="17.8" y2="15.5"/>',
        'zap'        => '<polygon points="13,2 4,14 11,14 10,22 20,10 12,10 13,2"/>',
        'clipboard'  => '<rect x="5" y="4" width="14" height="17" rx="1.5" ry="1.5"/><rect x="8.5" y="2" width="7" height="3.5" rx="1" ry="1"/><line x1="8" y1="10.5" x2="16" y2="10.5"/><line x1="8" y1="14" x2="16" y2="14"/><line x1="8" y1="17.5" x2="13" y2="17.5"/>',
        'id-badge'   => '<rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><circle cx="12" cy="8.5" r="3"/><line x1="8" y1="16" x2="16" y2="16"/><line x1="9" y1="19" x2="15" y2="19"/>',
        'briefcase'  => '<rect x="2.5" y="7" width="19" height="13" rx="2" ry="2"/><rect x="8.5" y="3.5" width="7" height="4" rx="1" ry="1"/><line x1="2.5" y1="13" x2="21.5" y2="13"/>',
        'users'      => '<circle cx="8" cy="8" r="3"/><polygon points="2,21 2,17 5,14 11,14 14,17 14,21"/><circle cx="17" cy="9" r="2.5"/><polygon points="13,21 13,18 15,16 20,16 22,18 22,21"/>',
        'search'     => '<circle cx="10.5" cy="10.5" r="6.5"/><line x1="15.5" y1="15.5" x2="21" y2="21"/>',
        'lock'       => '<rect x="4" y="10.5" width="16" height="10.5" rx="2" ry="2"/><path d="M7.5 10.5V7a4.5 4.5 0 0 1 9 0v3.5"/>',
    ];
    $path = $paths[$name] ?? $paths['star'];
    return '<svg class="' . htmlspecialchars($class) . '" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" '
        . 'fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" '
        . 'aria-hidden="true" focusable="false">' . $path . '</svg>';
}
