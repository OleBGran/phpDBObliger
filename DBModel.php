<?php
include_once("IModel.php");
include_once("Book.php");

/** The Model is the class holding data about a collection of books. 
 * @author Rune Hjelsvold
 * @see http://php-html.net/tutorials/model-view-controller-in-php/ The tutorial code used as basis.
 */
class DBModel implements IModel
{        
    /**
      * The PDO object for interfacing the database
      *
      */
    protected $db = null;  
    
    /**
	 * @throws PDOException
     */
    public function __construct($db = null)  
    {  
	    if ($db) 
		{
			$this->db = $db;
		}
		else
		{
			try{
            
			$this->db = new PDO('mysql:host=localhost;dbname=booklist;charset=utf8', 'root', '');
			}
			catch(PDOExeption $errors){
				print $errors->getMessage();
			}
		}
    }
    
    /** Function returning the complete list of books in the collection. Books are
     * returned in order of id.
     * @return Book[] An array of book objects indexed and ordered by their id.
	 * @throws PDOException
     */
    public function getBookList()
    {
		$booklist = array();
		try{
			foreach($this->db->query('SELECT * FROM booklist') as $row) {
				$booklist[] = new Book($row['title'],$row['author'],$row['description'],$row['id']) ; }
		}
		catch(PDOExeption $errors){
				print $errors->getMessage();
			}
		return $booklist;
    }
    
    /** Function retrieving information about a given book in the collection.
     * @param integer $id the id of the book to be retrieved
     * @return Book|null The book matching the $id exists in the collection; null otherwise.
	 * @throws PDOException
     */
    public function getBookById($id)
    {	try{
			$stmt = $this->db->prepare("SELECT * FROM booklist WHERE id=?");
			$stmt->execute(array($id));
			$rows = $stmt->fetch(PDO::FETCH_ASSOC);
			if($rows != ''){
				$book = new Book($rows['title'],$rows['author'],$rows['description'],$rows['id']); }
			else { $book = null; }
		}
		catch(PDOExeption $errors){
				print $errors->getMessage();
			}
		return $book;
    }
	

    
    /** Adds a new book to the collection.
     * @param $book Book The book to be added - the id of the book will be set after successful insertion.
	 * @throws PDOException
     */
    public function addBook($book)	
	{	
		try{
			if($book->title != '' && $book->author != ''){
				if($book->description == ''){$book->description = NULL;}
				$stmt = $this->db->prepare("INSERT INTO booklist (title, author, description) VALUES(?,?,?)");
				$stmt->execute(array($book->title, $book->author, $book->description));
				$book->id = $this->db->lastInsertId();}
				else{ $view = new ErrorView('Title or Author cannot be empty!');
					$view->create();}
		}
		catch(PDOExeption $errors){
			print $errors->getMessage();
		}
	}
    

    /** Modifies data related to a book in the collection.
     * @param $book Book The book data to be kept.
     * @todo Implement function using PDO and a real database.
     */
    public function modifyBook($book) {
		try{
			if($book->title != '' || $book->author != ''){
				$stmt = $this->db->prepare("UPDATE booklist SET title=?, author=?, description=? WHERE id=?");
				$stmt->bindValue(1, $book->title, PDO::PARAM_STR);
				$stmt->bindValue(2, $book->author, PDO::PARAM_STR);
				$stmt->bindValue(3, $book->description, PDO::PARAM_STR);
				$stmt->bindValue(4, $book->id, PDO::PARAM_INT);
				$stmt->execute();}
			else{$view = new ErrorView('Mangler title eller author');
				$view->create();}
		}
		catch(PDOExeption $errors){
				print $errors->getMessage();
			}
    }	

    /** Deletes data related to a book from the collection.
     * @param $id integer The id of the book that should be removed from the collection.
     */
    public function deleteBook($id)
    {
		try{
			$stmt = $this->db->prepare("DELETE FROM booklist WHERE id=?");
			$stmt->execute(array($id));
		}
		catch(PDOExeption $errors){
				print $errors->getMessage();
			}
    }
	
}

?>