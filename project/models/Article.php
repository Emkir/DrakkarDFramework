<?php
class Article extends ActiveRecord\Model{
    static $table_name = "article";
    static $belongs_to = array( array('user') );
}