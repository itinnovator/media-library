<?php

public function initProcessMedia()
{
    $uploader = new Uploader(Input::get('uploader'));
    $result = $uploader->handleUpload();

    if ($result && $uploader->getFileName()) {
      $image_name = $uploader->getFileName();
      $extension = pathinfo($image_name, PATHINFO_EXTENSION);
      $type = getMediaType($extension);
      $media = new \App\Objects\Media;
      $media->fill([
          'name' => $image_name,
          'type' => $type,
          'format' => $extension
      ]);
      $media->save();

      return jsonResponse('success', 'Media uploaded');
    }
}

public function initContentMediaLibrary($type = null)
{
    $media = $this->context->media
    ->where('type', $type)
    ->orderBy('id', 'desc')
    ->paginate(20);

    $data = [
      'media' => $media
    ];

    $html = getAdminTemplate('media/list-only', $data, true);

    return json('success', $html);
}
