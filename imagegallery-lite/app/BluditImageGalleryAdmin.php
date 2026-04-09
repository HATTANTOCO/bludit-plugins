<?php
/**
 * Image Gallery Lite - Image Gallery for Bludit3
 * Image gallery object for admin
 * 
 * @author     novafacile OÜ
 * @copyright  2022 by novafacile OÜ
 * @license    AGPL-3.0
 * @see        https://bludit-plugins.com
 * @notes      based on PHP Image Gallery novaGallery - https://novagallery.org
 * This program is distributed in the hope that it will be useful - WITHOUT ANY WARRANTY.
 */
namespace novafacile;

class BluditImageGalleryAdmin extends BluditImageGalleryLite {

  public function outputImagesAdmin($album){
    global $L, $security;
    $this->loadGallery($album);

    $imagesSort = $this->config['imagesSort'];

    // get images
    $images = $this->images($album, $imagesSort);
    
    // generate html output
    $html = '<div class="row w-100 text-left"><input type="hidden" id="jstokenCSRF" value="'.$security->getTokenCSRF().'">';
    $i = 0;
    foreach ($images as $image => $timestamp) {
      $html .= '<div class="col-6 col-md-3 mb-5 text-break imagegallery-images text-center" id="imagegallery-image-'.++$i.'">
                  <a href="'.$this->urlPath($album).$this->pathLarge.$image.'" class="image">
                    <img src="'.$this->urlPath($album).$this->pathThumbnail.$image.'" style="max-width: 100%;max-height:300px;">
                  </a>
                  <div class="text-left">'.$image.'<br>
                    <i class="fa fa-trash imagegallery-del-file" style="cursor:pointer"
                      data-album="'.$album.'" 
                      data-file="'.$image.'"
                      data-number="'.$i.'"
                      title="'.$L->get('Delete Image').'"></i>
                  </div>
                </div>';
      }
    $html .= '</div>';
    return $html;
  }

  public function outputUploadForm(){
    global $L;
    $html = '<p>'.$L->get('File names of uploaded images will be modified for maximum compatibility (e.g. no spaces & special characters, lower case). Images with the same file name will be overwritten.').'</p>';
    $html .= '<div class="dropzone mb-2" id="imagegallery-upload" style="border-style:dotted;"></div>';
    $html .= '<div class="w-100 text-center mb-5"><a href="'.$_SERVER['REQUEST_URI'].'" class="d-none btn btn-primary px-4" id="imagegallery-reload-button">'.$L->get('Reload page').'</a></div>';
    return $html;
  }

}