<?php

function crawl_page($keywords)
{
    // amazon search query url..
    $url = "http://www.amazon.in/s/url=search-alias%3Dstripbooks&field-keywords=".$keywords;
    $dom = new DOMDocument('1.0'); // new dom element
    @$dom->loadHTMLFile($url); // load html content
    $booklist = array(); 
    for ($i=0; $i < 16 ; $i++) { 
        $book = $dom->getElementById("result_".$i); // storing result elemnts in array
        if ($book) {
            $booklist[$i] = $book;
            $name = $book->getElementsByTagName("h2")[0]->nodeValue; // fetching book title
            $link = $book->getElementsByTagName('a')[0]->getAttribute('href');
            $links[$i] = $link;
            $link = substr($link, 0, strpos($link, 'ref=')); // link to buy book
            $isbn = substr($link, strpos($link, 'dp/')+3); //extracting the ISBN Number
            $isbnNumbers[$i] = rtrim($isbn, "/");
            $link = "<a href='".$link."' target= '_blank'><button class = 'btn btn-info'>Buy Now</button></a>";
            $links[$i] = $link;
            $dis =  substr($book->nodeValue, strlen($name));  // book discription
            if (strlen($name)>75){
                $name = substr($name, 0, 50)."..."; // trim long names
            }
            $nameList[$i]= $name;  // book title list
            $date = substr($dis, 0, strpos($dis, 'by')); // book publish date
            $pubDate[$i] = $date;   
            $dis = substr($dis, strlen($date)); 
            $pos = strpos($dis,',');
            if ($pos !== false) {
                $dis = substr_replace($dis,'',$pos,strlen(','));
            }
            preg_match_all('/\d+/', $dis, $matches); 
            $price[$i] = $matches[0][0];  // price list of books
            $dis = substr($dis, 0, strpos($dis, $matches[0][0]));
            $auth = substr($dis, 3, strlen($dis)-16); // auther of book
            $author[$i] = $auth; // Author List
        }   
    }
    return array($nameList, $author, $pubDate, $isbnNumbers, $price, $links);
}

if(isset($_GET['query'])) 
{
    $query = $_GET['query'];
    $query = preg_replace('/\s+/', ' ', $query);
    $query = str_replace(" ", "+", $query);
    $books = crawl_page($query);
    $col = sizeof($books); // number of rows
    $row = sizeof($books[0]); // number of columns
}
else{
    $books = 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Results (Amazon)</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <style>
        body {
            padding-top: 50px;
            padding-bottom: 20px;
        }
        #table{
            width: 80%;
            margin-left: 110px;
            margin-top: 50px;         
        }
        #alert-text{

            margin: 50px;
        }
     </style>
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Search Books</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <form class="navbar-form navbar-left" role="search" action=" <?php echo $_SERVER['PHP_SELF']; ?> " method = "get">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Search" name="query">
                </div>
                <button type="submit" class="btn btn-default">Search</button>
            </form>
        </div><!--/.navbar-collapse -->
      </div>
    </nav>

<?php
if ($books) {
?>
<div class="table-responsive" id="table">
<table border='1' class='table table-bordered table-hover'>
<thead>
        <tr>
          <th>Book Title</th>
          <th>Author</th> 
          <th>Published On:</th>
          <th>ISBN</th>
          <th>Price (Amazon)</th>
          <th>Buy Now</th>
        </tr> 
      </thead>
      <tbody>
    <?php

    for ($i=0; $i < $row ; $i++) { 
        echo "<tr>";
        for ($j=0; $j < $col; $j++) { 
            echo "<td>".$books[$j][$i]."</td>";
        }
        echo "</tr>";
    }
    ?>   
    </tbody>
</table>
</div>
<?php  
}
else{
    ?>
        <div class="alert alert-danger" id="alert-text" >
          <strong>Query!</strong> Please enter the book name..
        </div>

<?php  } ?>

</body>
</html>
