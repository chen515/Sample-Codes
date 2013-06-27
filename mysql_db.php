<?php
/**This class is used to connect to MySQL database,query data and handle errors.*/

class MySqlDB
{
  private $link;

  public function __construct($server, $username, $password, $database){
    //connect to database
    $this->connect($server, $username, $password, $database);
  }

  public function __destruct(){
    $this->disconnect();
  }

  /** Connect to database and select the database */
  public function connect($server='', $username='', $password='', $database='')
  {
    try{
      //connect to mysql
      $this->link = mysql_connect($server, $username, $password);

      //select the database
      mysql_select_db($database, $this->link);

      //throw an exception if connection is not initialized
      if(!$this->link){
        throw new CustomException('Can not start database connection', __CLASS__, 100);
      }
    }
    catch(CustomException $exception){
      echo $exception->__toString();
      exit();
    }
  }

  /** Execute a query */
  public function query($query)
  {
    return mysql_query($query, $this->link);
  }

  /**Fetch a result row as an associative array, a numeric array, or both */
  public function fetchArray($result, $arrayType = MYSQL_BOTH)
  {
    return mysql_fetch_array($result, $arrayType);
  }

  /**Fetch a result row as an enumerated array*/
  public function fetchRow($result)
  {
    return mysql_fetch_row($result);
  }

  /*Fetch a result row as an associative array*/
  public function fetchAssoc($result)
  {
    return mysql_fetch_assoc($result);
  }

  /*Fetch a result row as an object*/
  public function fetchObject($result)
  {
    return mysql_fetch_object($result);
  }

  /*Get the number of rows of the result set*/
  public function numRows($result)
  {
    return mysql_num_rows($result);
  }

  /*Returns the error message*/
  public function errorMessage()
  {
    return mysql_error($this->link);
  }

  /*Returns the error code*/
  public function errorCode()
  {
    return mysql_errno($this->link);
  }

  /*disconnect the database connection*/
  public function disconnect()
  {
    return mysql_close($this->link);
  }

}
?>
