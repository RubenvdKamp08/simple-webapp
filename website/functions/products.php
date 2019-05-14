<?php
class Product {

    private $db;

    function __construct($conn) {
        //Use construct so we can use the current connection easily.
        $this->db = $conn;
    }

    public function deleteProduct($id) {
        try {
            //Vraag productafbeelding op uit database zodat we die kunnen verwijderen van de server.
            $product = $this->db->prepare("SELECT image FROM products WHERE ID = " . $id);
            $product->execute();
            $productimage = $product->fetch(PDO::FETCH_ASSOC);

            //Verwijder productregel uit de database.
            $stmt = $this->db->prepare("DELETE FROM products WHERE ID = " . $id);
            $stmt->execute();
            //Verwijder productafbeelding van de server.
            unlink($_SERVER['DOCUMENT_ROOT'] . '/assets/images/products/' . $productimage['image']);

            return $stmt;
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function createProduct($title, $description, $price, $image, $category, $subcategory, $imagefile) {
        try {
            //Vraag extensie van geuploade bestand op en controleer als het een JPG, PNG of JPEG is. Geef anders een foutmelding en return false.
            $imageFileType = pathinfo(basename($image),PATHINFO_EXTENSION);
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                echo 'Product kon niet worden toegevoegd, controleer als de geuploade afbeelding wel een jpg, png of jpeg bestand is!';
                return false;
            } else {
                //Voeg nieuwe productregel toe aan de database.
                $stmt = $this->db->prepare("INSERT INTO products(title, description, price, image, categoryID, subcategoryID) 
                                                       VALUES(:title, :description, :price, :image, :categoryID, :subcategoryID)");

                $stmt->bindparam(":title", $title);
                $stmt->bindparam(":description", $description);
                $stmt->bindparam(":price", $price);
                $stmt->bindparam(":image", $image);
                $stmt->bindparam(":categoryID", $category);
                $stmt->bindparam(":subcategoryID", $subcategory);
                $stmt->execute();
                $this->uploadImage($image, $imagefile);
                echo 'Product is toegevoegd';
                return $stmt;
            }
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function editProduct($title, $description, $price, $category, $subcategory, $available, $image, $imagefile, $id) {
        try {
            //Controleer als er een nieuwe afbeelding is geupload of niet.
            if($imagefile['name'] == '') {
                $stmt = $this->db->prepare("UPDATE products SET title = :title, description = :description, price = :price, available = :available, categoryID = :categoryID, subcategoryID = :subcategoryID WHERE ID = " . $id);
            } else {
                $stmt = $this->db->prepare("UPDATE products SET title = :title, description = :description, price = :price, available = :available, image = :image, categoryID = :categoryID, subcategoryID = :subcategoryID WHERE ID = " . $id);
                $stmt->bindparam(":image", $image);

                //Haal de oude product afbeelding op uit de database.
                $product = $this->db->prepare("SELECT image FROM products WHERE ID = " . $id);
                $product->execute();
                $productimage = $product->fetch(PDO::FETCH_ASSOC);

                //Controleer als het bestand een afbeelding is of niet.
                $imageFileType = pathinfo(basename($image),PATHINFO_EXTENSION);
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                    return false;
                } else {
                    //Upload de nieuwe afbeelding naar de server.
                    $this->uploadImage($image, $imagefile);
                    //Verwijder de oude afbeelding van de server
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/assets/images/products/' . $productimage['image']);
                }
            }

            $stmt->bindparam(":title", $title);
            $stmt->bindparam(":description", $description);
            $stmt->bindparam(":price", $price);
            $stmt->bindparam(":available", $available);
            $stmt->bindparam(":categoryID", $category);
            $stmt->bindparam(":subcategoryID", $subcategory);
            $stmt->execute();

            return true;
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getProducts($searchquery, $currentRow, $limit) {
        try {
            //Haal alle producten op uit de database gebaseeerd op de zoekquery.
            $data = $this->db->prepare('SELECT p.*, pc.name AS category, pcs.name AS subcategory FROM products AS p INNER JOIN productcategory AS pc ON p.categoryID = pc.ID LEFT JOIN productsubcategory AS pcs ON p.subcategoryID = pcs.ID ' . $searchquery .' LIMIT ' . $currentRow . ', ' . $limit);
            $data->execute();

            return $data;
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getProduct($id) {
        try {
            //Haal het product op uit de database.
            $data = $this->db->prepare('SELECT p.*, pc.name as category FROM products AS p INNER JOIN productcategory AS pc ON p.categoryID = pc.ID WHERE p.ID = ' . $id);
            $data->execute();

            return $data;
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getCategories() {
        try {
            //Haal alle productcategorieeen op.
            $data = $this->db->prepare('SELECT * FROM productcategory');
            $data->execute();

            return $data;
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function getSubcategories() {
        try {
            //Haal alle product subcategorieeen op.
            $data = $this->db->prepare('SELECT * FROM productsubcategory');
            $data->execute();

            return $data;
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function uploadImage($image, $imagefile) {
        //Zoek afbeelding map en bestand
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/assets/images/products/";
        $target_file = $target_dir . basename($image);
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

        //Controleer als bestand wel een afbeelding is.
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        } else {
            move_uploaded_file($imagefile["tmp_name"], $target_file);
        }
    }
}