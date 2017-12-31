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

function generateMedia($id, $mediaList, $type = 'image', $var_type = 'string')
{
    if (!isset($mediaList)) {
      return;
    }

    $ids = '';

    if (count($mediaList) > 1) {
      foreach ($mediaList as $key => $media) {
        $media_opt = $media;
        if (!$media_opt) {
          continue;
        }

        if ($ids) {
          $ids .= ','.$media_opt->id;
        } else {
          $ids = ''.$media_opt->id;
        }
        $html = generateMediaHTML($id, $media_opt);
      }
    } else {
      $ids = $mediaList->id;
      $html = generateMediaHTML($id, $mediaList);
    }

    $name = $id;
    if ($var_type == 'array') {
      $name = $id.'[]';
    }

    if ($type != 'video'){
      $html .='<input type="hidden" id="' .$id. '" name="' .$name. '" value="' .$ids. '">';
    }

    $html .='</div>';

    return $html;
}

function generateMediaHTML($id, $media_opt)
{
    $type = $media_opt->type;
    $html = '<div id="'.$id.'_wrapper" class="preview-wrapper">';
    if ($type == 'image') {
      $html .='<div class="selected-img-preview"> <img id_media="'.$media_opt->id.'" src="'.getMedia($media_opt->name, '150, 150').'" class="full select-library-img"> <div class="img-action"> <button type="button" class="btn btn-success" id="edit-media-img" id_media="'.$media_opt->id.'">Edit</button> <button type="button" class="btn btn-danger" id="delete-media-img" id_media="'.$media_opt->id.'">Delete</button>
      </div></div>';

    } elseif ($type == "pdf") {
      $html .='<div class="selected-img-preview"><a type="pdf" class="pdf-list select-library-img" id_media="'.$media_opt->id.'" href="'.getMedia($media_opt->name, '150, 150').'"> <i class="mdi mdi-file-pdf"></i> <p class="file_name_pdf">'.$media_opt->name.'</p></a> <div class="img-action"> <button type="button" class="btn btn-success" id="edit-media-img" id_media="'.$media_opt->id.'">Edit</button> <button type="button" class="btn btn-danger" id="delete-media-img" id_media="'.$media_opt->id.'">Delete</button> </div></div>';

    } elseif ($type == "video") {
      $html .= '<div class="selected-img-preview">';
      if ($media_opt->type == 'video' && $media_opt->format == 'embed') {
        $html .='<div class="select-library-img video-selector" id_media="'.$media_opt->id.'"></div>';
        $html .= $media_opt->name;
      } elseif ($media_opt->type == 'video' && $media_opt->format != 'embed') {
        $html .='<div class="select-library-img video-selector" id_media="'.$media_opt->id.'"></div><video width="150" height="135" controls><source src="'.getMedia($media_opt->name, '150, 150').'" type="video/'.$media_opt->format.'"></video>';
      }
      $html .= '</div>';
    }

      return $html;
}
