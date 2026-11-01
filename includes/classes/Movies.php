<?php

  class Movies {
    private $conn;

    public function __construct($conn) {
      $this->conn = $conn;
    }

    // display full list
    // public function movieList($type, $category, $rating) {
    public function movieList() { 
		$notice = '';

		// $type = $type;
		// $category = $category;
		// $rating = $rating;

		$sql = $this->conn->prepare("SELECT * FROM movie_list");
		// $sql = $this->conn->prepare("SELECT * FROM movielist");
		// $sql->bindValue(':mov_type', $type, PDO::PARAM_STR);
		// $sql->bindValue(':mov_catgory', $category, PDO::PARAM_STR);
		// $sql->bindValue(':mov_rating', $rating, PDO::PARAM_STR);
		$sql->execute();
		// $sql->execute(
		//   array(
		//     'mov_type'        => $type,
		//     'mov_category'    => $category,
		//     'mov_rating'      => $rating
		//   )
		// );

		// execute sql
		// if ($sql->execute()) {
        // echo "Record updated successfully";
        
        $html = "<tbody>";

        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
			$html .= "<tr>";
			$html .= "<td>" . $row["title"] . "</td>";
			$html .= "<td>" . $row["type"] . "</td>";
			$html .= "<td>" . $row["category"] . "</td>";
			$html .= "<td>" . $row["rating"] . "</td>";
			$html .= "</tr>";
        }

        return $html . "</tbody>";

        // echo 'hello';

		// } else {
		// echo "Error updating record: " . $conn->error;
		// $notice =  "<div>
		//             <p>Error updating record: " . $conn->error . "</p>
		//           </div>";
		// }

    }

    // display category / dynamic
    // public function movieType($type) {

    //   $sql = $this->conn->prepare("SELECT * FROM movielist WHERE mov_type='$type'");
    //   $sql->execute();

    //   $html = '<table>';

    //   while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
    //     $html .= '<tr>';
    //     $html .= '<td>' . $row["mov_title"] . '</td>';
    //     $html .= '<td>' . $row["mov_type"] . '</td>';
    //     $html .= '<td>' . $row["mov_category"] . '</td>';
    //     $html .= '<td>' . $row["mov_rating"] . '</td>';
    //     $html .= '</tr>';
    //   }

    //   return $html . '</table>';

    // }

    // display category / dynamic
    // public function movieCategory($category) {

    //   $sql = $this->conn->prepare("SELECT * FROM movielist WHERE mov_category=''");
    //   $sql->execute();

    //   $html = '<table>';

    //   while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
    //     $html .= '<tr>';
    //     $html .= '<td>' . $row["mov_title"] . '</td>';
    //     $html .= '<td>' . $row["mov_type"] . '</td>';
    //     $html .= '<td>' . $row["mov_category"] . '</td>';
    //     $html .= '<td>' . $row["mov_rating"] . '</td>';
    //     $html .= '</tr>';
    //   }

    //   return $html . '</table>';

    // }
    
  }

?>