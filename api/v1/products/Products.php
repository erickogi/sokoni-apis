<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 12/21/2017
 * Time: 6:22 AM
 */
/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Kogi Eric
 *
 */

/**
 * Change the queries in this methods to correspond with your sql queries
 * this is also the class you should add any other crud method for all the api calls
 *
 *
 * NOTE YOU NEED TO INTRODUCE YOUR CONNECTION TO DB HERE BY INCLUDING THE CONNECT.PHP FILE
 *
 *
 * IE private $conn
 *
 * @author Kogi Eric
 */


/**
 * Class DbHandler
 */

class ProductsDbHandler
{


    /**
     * @var PDO
     */
    /**
     * DbHandler constructor.
     */


    /**
     * @return mixed
     */

    /**
     * @var PDO
     */
    private $conn;

    /**
     * DbHandler constructor.
     */
    function __construct()
    {
       // require_once dirname(__FILE__) . '../configs/DbConnect.php';
        require_once dirname(__FILE__) . '/../configs/DbConnect.php';
       // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }


    public function promotionalSliders(){
       $stmt = $this->conn->prepare("SELECT * FROM promotional_sliders  ");
          
          $stmt->execute();
          
          
          $result = $stmt->get_result();
          $num_rows = $result->num_rows;
          
          $stmt->close();
  
        
          
          if($num_rows>0){
              while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            return $results;
          }else{
              return NULL;
  
          }
          
        
          
    }


    public function trendingProducts(){
       

        $stmt = $this->conn->prepare("SELECT * FROM products ");
        
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            return $results;
         }else{
             return NULL;
 
         }
         
       
         
    }
    public function rate($cust_id,$product_id,$rating){
        $stmt = $this->conn->prepare("INSERT INTO product_ratings

        (product_ratings.cust_id, product_ratings.product_id, product_ratings.rating)
           
        VALUES(?,?,?)");

        $stmt->bind_param("iis", $cust_id,$product_id,$rating);

        $stmt->execute();

        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

       
        if($num_rows>0){
            return false;
        }else{
            return true;

        }
    }

    public function getRatingAndFavourite($products,$cust_id){
       
           foreach($products as $key => &$product) {
            $product["rating"]=$this->getRating($product["id"]);
            $product["favorite"]=$this->getFavourite($product["id"],$cust_id);
           
           
        }
           return $products;
    }
    public function getProductRatingAndFavourite($product,$cust_id){
       
         $product["rating"]=$this->getRating($product["id"]);
         $product["favorite"]=$this->getFavourite($product["id"],$cust_id);
        
        
     
        return $product;
 }
    public function getRating($productId){

       // SELECT AVG(P1_Score)
        $stmt = $this->conn->prepare("SELECT AVG(rating) FROM product_ratings WHERE  product_id= ?   ");
        $stmt->bind_param("i",$productId);
        
        $stmt->execute();
        
        
        $result = $stmt->get_result();
        $num_rows = $result->num_rows;
        
        $stmt->close();

       
        
        if($num_rows>0){
            //$row["value"]
            $data=$result->fetch_assoc()["AVG(rating)"];
            if($data!=null){
                return $this->MRound($data,4);
            }
            return 0.0;
        }else{
            return 0.0;

        }
    }
    function MRound($num,$parts) {
        $res = $num * $parts;
        $res = round($res);
        return $res /$parts;
    }
    public function getFavourite($productId,$cust_id){
        $stmt = $this->conn->prepare("SELECT * FROM customers_favorites WHERE  cust_id=? AND product_id= ?  ");
        $stmt->bind_param("ii", $cust_id,$productId);
        
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            
            return true;
         }else{
             return false;
 
         }
        
    }

    public function trendingProductsPagged($last,$limit){
       

        $stmt = $this->conn->prepare("SELECT * FROM products  LIMIT ?  ");
        $stmt->bind_param("i", $last);
        
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            return $results;
         }else{
             return NULL;
 
         }
         
       
         
    }


    public function topCategories(){
       
        $stmt = $this->conn->prepare("SELECT * FROM product_subcategory_items   LIMIT 10");
         
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            return $results;
         }else{
             return NULL;
 
         }
         
       
         
    }


    public function products($subCategoryItemId){
       
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE sub_category_item_id = ?  ");
         $stmt->bind_param("i", $subCategoryItemId);
        
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            return $result->fetch_assoc();
         }else{
             return NULL;
 
         }
         
       
         
    }

    public function product($product_id){
       
         $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?   LIMIT 1 ");
         $stmt->bind_param("i", $product_id);
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            return $result->fetch_assoc();
         }else{
             return NULL;
 
         }
         
       
         
    }


    public function attachProductDetails($product,$cust_id){

        $product["rating"]=$this->getRating($product["id"]);
        $product["favorite"]=$this->getFavourite($product["id"],$cust_id);


       


         $product["productImages"]=$this->productImages($product["id"]);
         $product["productStore"]=$this->productStore($product["store_id"]);


         $storeProducts=$this->storeProduct($product["store_id"]);
         $product["storeProducts"]=$this->getRatingAndFavourite($storeProducts,$cust_id);



         $relatedProducts=$this->relatedProduct($product["sub_category_item_id"],$this->productTags($product["id"]));
         $product["relatedProducts"]=$this->getRatingAndFavourite($storeProducts,$cust_id);

         
         $product["variations"]=$this->productsVariations($product);
        
        
        
        
     
        return $product;
    }
    public function productsTags(){
       
        $stmt = $this->conn->prepare("SELECT * FROM tags  ");
        
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            return $results;
         }else{
             return NULL;
 
         }
         
       
         
    }

    public function productTags($productId){
       
        $stmt = $this->conn->prepare("SELECT * FROM tags  ");
        
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            return $results;
         }else{
             return NULL;
 
         }
         
       
         
    }


    public function productImages($productId){
       
        $stmt = $this->conn->prepare("SELECT * FROM product_images WHERE product_id = ?  ");
        $stmt->bind_param("i", $productId);
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            return $results;
         }else{
             return NULL;
 
         }
         
       
         
    }

    public function productReviews($productId){
       
        $stmt = $this->conn->prepare("SELECT * FROM tags  ");
        
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            return $results;
         }else{
             return NULL;
 
         }
         
       
         
    }

    public function productDetails($productId){
       
        $stmt = $this->conn->prepare("SELECT * FROM tags  ");
        
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            return $results;
         }else{
             return NULL;
 
         }
         
       
         
    }

    public function productStore($storeId){
       
        $stmt = $this->conn->prepare("SELECT * FROM stores WHERE id = ? LIMIT 1 ");
         $stmt->bind_param("i", $storeId);
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            return $result->fetch_assoc();
         }else{
             return NULL;
 
         }
         
       
         
    }
    
    public function productCustomReviews($productId){
       
        $stmt = $this->conn->prepare("SELECT * FROM product_reviews WHERE product_id = ? ORDER BY created_at DESC ");
        $stmt->bind_param("i", $productId);
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            return $results;
         }else{
             return NULL;
 
         }
       
         
    }
    public function productCustomDetails($productId){
       
        $stmt = $this->conn->prepare("SELECT * FROM product_details WHERE product_id = ?  ");
        $stmt->bind_param("i", $productId);
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            return $results;
         }else{
             return NULL;
 
         }
       
         
    }
    

    public function productsVariations($productId){
       
        $stmt = $this->conn->prepare("SELECT * FROM product_variants WHERE product_id = ? ");
        $stmt->bind_param("i", $productId);
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
                $row["variants"]=$this->variantItems($row["id"]);
                $results[] = $row;
            }
            return $results;
         }else{
             return NULL;
 
         }
       
         
    }

    public function variantItems($variantId){
       
        $stmt = $this->conn->prepare("SELECT * FROM variants WHERE variant_id = ? ");
        $stmt->bind_param("i", $variantId);
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            return $results;
         }else{
             return NULL;
 
         }
       
         
    }

    public function storeProduct($storeId){
       
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE store_id = ?  LIMIT 10");
        $stmt->bind_param("i", $storeId);
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }
            return $results;
         }else{
             return NULL;
 
         }
       
         
    }

    public function relatedProduct($subCategoryItemId,$tags){
       
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE sub_category_item_id = ?  LIMIT 10 ");
         $stmt->bind_param("i", $subCategoryItemId);
        
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            return $result->fetch_assoc();
         }else{
             return NULL;
 
         }
         
       
         
    }

public function favoriteProducts($cust_id){
       
        $stmt = $this->conn->prepare("SELECT  * FROM customers_favorites WHERE cust_id = ?  GROUP BY product_id  ");
        $stmt->bind_param("i", $cust_id);
        
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
              $id=$row["product_id"];

              
              $results[]=$this->getProductRatingAndFavourite($this->product($id),$cust_id);

            }
            return $results;
            
         }else{
             return NULL;
 
         }
         
       
         
    }
    
    
    
    
    public function getProductIdByTags($tags){
       
        $query = "SELECT * FROM product_search_tags WHERE tag LIKE ?";

       
        $stmt=$this->conn->prepare($query);
        
       

        $stmt->bind_param("s","e");
           foreach($tags as $key => $tag){
           // $stmt->bind_param($key+1, '%'.$tag.'%');
           //echo $tag;
           $ias="s";
           $par='%'.$tag.'%';
            $stmt->bind_param($key+1,$tag );
          }
        $stmt->execute();
        $result = $stmt->get_result();
             $num_rows = $result->num_rows;
             $stmt->close();
     
           
             
             if($num_rows>0){
                while ($row = $result->fetch_assoc()) {
                  $id=$row["product_id"];
                  $results[]=$row;
    
                }
                return $results;
                
             }else{
                 return "sff";
     
             }
             
             
    }


    public function searchByTag($tag){
        $stmt = $this->conn->prepare("SELECT  * FROM product_search_tags WHERE tag LIKE ?  ");
        $keyword = "%".$tag."%";
        $stmt->bind_param("s", $keyword);
        
         $stmt->execute();
         
         
         $result = $stmt->get_result();
         $num_rows = $result->num_rows;
         
         $stmt->close();
 
       
         
         if($num_rows>0){
            while ($row = $result->fetch_assoc()) {
              $id=$row["product_id"];

              
              echo "fg".$id;
              $results[]=$this->getProductRatingAndFavourite($this->product($id),$cust_id);

            }
            return $results;
            
         }else{
             return "sdad";
 
         }
    }


}







?>
