<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\Common\Collections\Collection;

interface ImageContainerInterface {
    /**
     * @return mixed
     */
    public function addImage(Image $image);

    /**
     * @return mixed
     */
    public function removeImage(Image $image);

    public function hasImage(Image $image) : bool;

    /**
     * @param Collection|Image[] $images
     *
     * @return mixed
     */
    public function setImages($images);

    /**
     * @return Collection|Image[]
     */
    public function getImages();
}
