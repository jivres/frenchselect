<?php

namespace B2bBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\HttpFoundation\File\UploadedFile;


/**
 * MyFile
 *
 * @ORM\Table(name="myfile")
 * @ORM\Entity(repositoryClass="B2bBundle\Repository\MyFileRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MyFile
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\File(maxSize="10M")
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;
    private $oldUrl;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
    * Sets file.
    *
    * @param UploadedFile $file
    */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->url)) {
            // store the old name to delete after the update
            $this->orlUrl = $this->url;
            $this->url = null;
        } else {
            $this->url = 'initial';
        }
    }

    /**
    * Get file.
    *
    * @return UploadedFile
    */
    public function getFile()
    {
        return $this->file;
    }

    /**
    * @ORM\PrePersist()
    * @ORM\PreUpdate()
    */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = md5(uniqid()).'.'.$this->file->guessExtension();
            $this->url = $filename;
        }
    }

    /**
    * @ORM\PostPersist()
    * @ORM\PostUpdate()
    */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->url);

        // check if we have an old image
        if (isset($this->orlUrl)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->orlUrl);
            // clear the temp image path
            $this->orlUrl = null;
        }
        $this->file = null;
    }

    /**
    * @ORM\PostRemove()
    */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if ($file) {
            unlink($file);
        }
    }





    public function getAbsolutePath()
    {
        return null === $this->url
            ? null
            : $this->getUploadRootDir().'/'.$this->url;
    }

    public function getWebPath()
    {
        return null === $this->url
            ? null
            : $this->getUploadDir().'/'.$this->url;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads';
    }


    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set url.
     *
     * @param string $url
     *
     * @return MyFile
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}
