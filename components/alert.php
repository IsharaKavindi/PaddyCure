<?php

function renderAlert($alertInfo = array('type' => 'SUCCESS', 'message' => 'Success', 'title' => 'Success', 'show' => false))
{
    if (isset($alertInfo) && !empty($alertInfo) && $alertInfo['show']) {
        echo <<< HTML

        <div class="alert alert-{$alertInfo['type']}" role="alert">
            <strong>{$alertInfo['title']}</strong> {$alertInfo['message']}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        HTML;
    }
    return;
}
