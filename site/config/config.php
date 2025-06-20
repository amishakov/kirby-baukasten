<?php

use Kirby\Cms\Pages;
use Kirby\Http\Url;
use tobimori\Spielzeug\Menu;

require_once dirname(__DIR__) . '/plugins/kirby3-dotenv/global.php';

loadenv([
	'dir' => realpath(dirname(__DIR__, 2)),
	'file' => '.env',
]);

return [
	'debug' => json_decode(env('KIRBY_DEBUG')),
	'routes' => require __DIR__ . '/routes.php',
	'thumbs' => require __DIR__ . '/thumbs.php',
	'yaml.handler' => 'symfony',
	'date.handler' => 'intl',
	'url' => env("APP_URL"),
	'tobimori.trawl' => [
		'sourceLanguage' => 'en',
		'languages' => ['de', 'en']
	],
	'tobimori.seo' => require __DIR__ . '/seo.php',
	'tobimori.dreamform' => require __DIR__ . '/dreamform.php',
	'tobimori.icon-field' => [
		// use generated sprite, requires pnpm run build
		// to generate (dev sprite doesn't work since it's not in the public folder)
		'folder' => '',
		'sprite' => fn() => Url::path(vite()->useHotFile('.never')->asset('assets/sprite.svg'))
	],
	/** Email */
	'email' => require __DIR__ . '/email.php',
	'auth' => [
		'methods' => ['password', 'password-reset'],
		'challenge' => [
			'email' => [
				'from' => env("KIRBY_MAIL_FROM"),
				'subject' => 'Login-Code'
			]
		]
	],
	/** Caching */
	'cache' => [
		'pages' => [
			'active' => json_decode(env('KIRBY_CACHE')),
		]
	],
	/** Build Env / Vite / etc. */
	'bnomei.dotenv.dir' => fn() => realpath(kirby()->roots()->base()),
	'lukaskleinschmidt.kirby-laravel-vite.buildDirectory' => 'dist',
	/** Panel */
	'johannschopplich.plausible.sharedLink' => env('PLAUSIBLE_SHARED_LINK'),
	'distantnative' => [
		'retour' => [
			'config' =>  __DIR__ . './../../data/storage/retour/config.yml',
			'database' =>  __DIR__  . './../../data/storage/retour/log.sqlite'
		]
	],
	'panel' => [
		'vue' => [
			'compiler' => false
		],
		'menu' => fn() => array_merge(
			[
				'site' => Menu::site(),
			],
			Menu::favorites(kirby()->user()?->favorites()->toPages() ?? new Pages([])),
			[
				'-',
				'images' => Menu::page(null, 'file-image', page('page://images')),
				'files' => Menu::page(null, 'file-word', page('page://files')),
				'-',
				'users',
				'plausible',
				'retour',
				'queues',
			]
		)
	],
	'ready' => fn() => [
		'panel' => [
			'favicon' => option('debug') ? 'static/panel/favicon-dev.svg' : 'static/panel/favicon-live.svg',
			'css' => vite('src/styles/panel.css'),
		],
	]
];
