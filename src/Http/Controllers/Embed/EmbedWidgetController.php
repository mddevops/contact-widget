<?php

namespace SiteApps\ContactWidget\Http\Controllers\Embed;

use SiteApps\ContactWidget\Http\Controllers\Controller;
use SiteApps\ContactWidget\Services\Social\SocialWidgetService;
use Illuminate\Http\Response;

class EmbedWidgetController extends Controller
{
    public function __invoke(): Response
    {
        $widget = SocialWidgetService::active();

        abort_unless($widget, 404);

        return response()->view('contact-widget::widget', [
            'widget' => $widget,
        ]);
    }
}
