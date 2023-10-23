<?php

return [
    'mode'                     => '',
    'format'                   => 'A4',
    'default_font_size'        => '16',
    'default_font'             => "'allura-regular','Segoe UI', Tahoma, Geneva, Verdana, sans-serif ",
    'margin_left'              => 20,
    'margin_right'             => 20,
    'margin_top'               => 100,
    'margin_bottom'            => 0,
    'margin_header'            => 0,
    'margin_footer'            => 0,
    'orientation'              => 'L',
    'title'                    => 'Laravel mPDF',
    'subject'                  => '',
    'author'                   => '',
    'watermark'                => '',
    'show_watermark'           => false,
    'show_watermark_image'     => false,
    'watermark_font'           => 'sans-serif',
    'display_mode'             => 'fullpage',
    'watermark_text_alpha'     => 0.1,
    'watermark_image_path'     => '',
    'watermark_image_alpha'    => 0.2,
    'watermark_image_size'     => 'D',
    'watermark_image_position' => 'P',
    'display_mode'         => 'fullpage',
    'auto_language_detection'  => true,
    'temp_dir'                 => rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR),
    'pdfa'                     => false,
    'pdfaauto'                 => false,
    'custom_font_dir' => base_path('storage/fonts/'), // don't forget the trailing slash!
	'custom_font_data' => [
		'allura-regular' => [
			'R'  => 'Allura-Regular.ttf',    // regular font
		]
	]
];
