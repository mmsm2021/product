<?php 

class Product{

    private $id;

    private $name;

    private $location_id;

    private $price;

    private $discount_price;

    private $discount_from;

    private $discount_to;

    private $status;

    private $deleted_at;

    private $updated_at;

    private $created_at;

    private $attributes;

    private $description;

    private $unique_id;


    public function __construct(){

    }

    public function getName(){
        return $this->name;
    }
    public function setName(){

    }

    public function getLocationId(){
        return $this->location_id;
    }
    public function setLocationId(){

    }

    public function getPrice(){
        return $this->price;
    }
    public function setPrice(){

    }

    public function getDiscountPrice(){
        return $this->discount_price;
    }
    public function setDiscountPrice(){

    }

    public function getDiscountFrom(){
        return $this->discount_from;
    }
    public function setDiscountFrom(){

    }

    public function getDiscountTo(){
        return $this->discount_to;
    }
    public function setDiscountTo(){

    }

    public function getStatus(){
        return $this->status;
    }
    public function setStatus(){

    }

    public function getDeletedAt(){
        return $this->deleted_at;
    }
    public function setDeletedAt(){

    }

    public function getUpdatedAt(){
        return $this->updated_at;
    }
    public function setUpdatedAt(){

    }

    public function getCreatedAt(){
        return $this->created_at;
    }
    public function setCreatedAt(){

    }

    public function getAttributes(){
        return $this->attributes;
    }
    public function setAttributes(){
        
    }

    public function getDescription(){
        return $this->description;
    }
    public function setDescription(){

    }

    public function getUniqueId(){
        return $this->unique_id;
    }
    public function setUniqueId(){
        
    }
}

?>