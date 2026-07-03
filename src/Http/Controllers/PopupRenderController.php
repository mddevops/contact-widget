<?php

namespace SiteApps\ContactWidget\Http\Controllers;

use SiteApps\ContactWidget\Models\Popup;
use Illuminate\Http\Response;

class PopupRenderController extends Controller
{
    public function __invoke(Popup $popup): Response
    {
        abort_unless($popup->is_active, 404);

        return response()->view('contact-widget::popup-root', [
            'popup' => $popup,
        ]);
    }
}
