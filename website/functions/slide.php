<?php
    class Slide {

        private $db;

        function __construct($conn) {
            //Use construct so we can use the current connection easily.
            $this->db = $conn;
        }

        public function deleteSlide($id) {
            try {
                //Haal slide afbeeldign op.
                $slide = $this->db->prepare("SELECT image FROM homepageslider WHERE ID = " . $id);
                $slide->execute();
                $slideimage = $slide->fetch(PDO::FETCH_ASSOC);

                //Verwijder slide uit database
                $stmt = $this->db->prepare("DELETE FROM homepageslider WHERE ID = " . $id);
                $stmt->execute();
                //Verwijder slide afbeelding van de server.
                unlink($_SERVER['DOCUMENT_ROOT'] . '/assets/images/slider/' . $slideimage['image']);

                return $stmt;
            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function createSlide($title, $image, $imagefile) {
            try {
                //Controleer als bestand een afbeelding is.
                $imageFileType = pathinfo(basename($image),PATHINFO_EXTENSION);
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                    echo 'Afbeelding kon niet worden toegevoegd, controleer als de geuploade afbeelding wel een jpg, png of jpeg bestand is!';
                    return false;
                } else {
                    //Voeg slide toe aan de database.
                    $stmt = $this->db->prepare("INSERT INTO homepageslider(title, image) 
                                                       VALUES(:title, :image)");

                    $stmt->bindparam(":title", $title);
                    $stmt->bindparam(":image", $image);
                    $stmt->execute();
                    //Upload afbeelding naar de server.
                    $this->uploadImage($image, $imagefile);
                    echo 'Slide is toegevoegd';

                    return $stmt;
                }
            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function editSlide($title, $image, $imagefile, $id) {
            try {
                //Controleer als afbeelding gewijzigd is of niet.
                if($imagefile['name'] == '') {
                    $stmt = $this->db->prepare("UPDATE homepageslider SET title = :title WHERE ID = " . $id);
                } else {
                    //Voer update query uit.
                    $stmt = $this->db->prepare("UPDATE homepageslider SET title = :title, image = :image WHERE ID = " . $id);
                    $stmt->bindparam(":image", $image);

                    //Haal de slide afbeelding op.
                    $slide = $this->db->prepare("SELECT image FROM homepageslider WHERE ID = " . $id);
                    $slide->execute();
                    $slideimage = $slide->fetch(PDO::FETCH_ASSOC);

                    //Controleer als bestand wel een afbeelding is.
                    $imageFileType = pathinfo(basename($image),PATHINFO_EXTENSION);
                    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                        return false;
                    } else {
                        //Upload afbeelding naar de server.
                        $this->uploadImage($image, $imagefile);
                        //Verwijder oude afbeelding van de server.
                        unlink($_SERVER['DOCUMENT_ROOT'] . '/assets/images/slider/' . $slideimage['image']);
                    }
                }

                $stmt->bindparam(":title", $title);
                $stmt->execute();

                return true;
            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function getSlides() {
            try {
                //Haal alle slides op.
                $data = $this->db->prepare('SELECT * FROM homepageslider');
                $data->execute();

                return $data;
            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function getSlide($id) {
            try {
                //Haal slide op met gegeven ID.
                $data = $this->db->prepare('SELECT * FROM homepageslider WHERE ID = ' . $id);
                $data->execute();

                return $data;
            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        }

        public function uploadImage($image, $imagefile) {
            //Zoek afbeelding map en bestand
            $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/assets/images/slider/";
            $target_file = $target_dir . basename($image);
            $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

            //Controleer als bestand wel een afbeelding is.
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            } else {
                move_uploaded_file($imagefile["tmp_name"], $target_file);
            }
        }
    }