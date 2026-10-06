<?php

namespace App\Http\Controllers;

use App\Models\Property;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $pages = collect(['home', 'buy', 'rent', 'commercial', 'list.create', 'about', 'contact'])
            ->map(fn ($name) => ['url' => route($name), 'updated' => null]);

        $properties = Property::published()->get(['slug', 'updated_at'])
            ->map(fn ($p) => ['url' => route('properties.show', $p), 'updated' => $p->updated_at]);

        return response()
            ->view('sitemap', ['entries' => $pages->concat($properties)])
            ->header('Content-Type', 'application/xml');
    }
}
