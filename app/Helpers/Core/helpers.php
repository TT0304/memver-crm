<?php
    use App\Modules\ViewRenderEventManager;

    if (! function_exists('core')) {
        function core()
        {
            return app()->make('core');
        }
    }

    if (! function_exists('view_render_event')) {
        function view_render_event($eventName, $params = null)
        {
            app()->singleton(ViewRenderEventManager::class);
    
            $viewEventManager = app()->make(ViewRenderEventManager::class);
    
            $viewEventManager->handleRenderEvent($eventName, $params);
    
            return $viewEventManager->render();
        }
    }

    // if (! function_exists('bouncer')) {
    //     function bouncer()
    //     {
    //         return app()->make('bouncer');
    //     }
    // }
?>