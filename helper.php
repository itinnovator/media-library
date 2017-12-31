<?php

function resize($path, $image, $size1, $size2)
{
    if (!$size1 || !$size2) {
        return;
    }

    $size1 = trim($size1);
    $size2 = trim($size2);

    $background = Image::canvas($size1, $size2);

    $size_folder = $size1.'X'.$size2;
    if (!file_exists($path.$size_folder)) {
        mkdir($path.$size_folder);
    }

    $img = Image::make($path.$image);
    $img->resize($size1, $size2, function ($constraint) {
       $constraint->aspectRatio();
    });

    // Fill up the blank spaces with transparent color
    if ($img) {
        $img->resizeCanvas(null, $size2, 'center', false, array(255, 255, 255, 0));
        //$img->resize(intval($size1),intval($size2));
        // add callback functionality to retain maximal original image size
        //$background->insert($image, 'center');
        //$img->fit(intval($size1));
        $img->save($path.$size_folder.'/'.$image);
    }

    return url('storage/media/image') . '/' . $size_folder.'/';
}

function getMediaType($extension)
{
    $type = 'embeded';

    if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
      $type = 'image';
    }

    if (in_array($extension, ['mp4', 'MP4', 'FLV', 'flv', 'avi', 'AVI', 'wmv', 'vob'])) {
      $type = 'video';
    }

    if (in_array($extension, ['pdf'])) {
      $type = 'pdf';
    }

    if (in_array($extension, ['mp3'])) {
      $type = 'audio';
    }

    return $type;
}

function getTemplate($view, $data = array(), $print = false)
{
    $context = \App\Classes\Context::getContext();
    $default_data = [
      'context' => $context
    ];

    $data = array_merge($default_data, $data);


    $html = view('front' . "/" . config('settings.front_theme') . "/" . $view, $data);

    if ($print) {
        $core = $context->core;
        $core->prepareHTML($html);
        return $core->buildHTML();
    }

    return $html;
}

function getAdminTemplate($view, $data = array(), $print = false)
{
    $context = \App\Classes\Context::getContext();
    $default_data = [
      'context' => $context
    ];

    $data = array_merge($default_data, $data);


    $html = view('admin' . "/" . config('settings.admin_theme') . "/templates/" . $view, $data);

    if ($print) {
        $core = $context->core;
        $core->prepareHTML($html);
        return $core->buildHTML();
    }

    return $html;
}
