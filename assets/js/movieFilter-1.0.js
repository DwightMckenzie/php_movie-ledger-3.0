/**
 * movie filter
 * 
 * @param string option = onload, reset, filter
 * onload = on page load all movies show
 * reset = reset to show all movies and reset the select options
 * filter = use defined
 * 
 * AJAX
 * 
 */
function movieFilter(option){
  "use strict";

  const searchTitle = document.getElementById('search-title');
  const movieType = document.getElementById('movie-type');
  const movieCategory = document.getElementById('movie-category');
  const movieRating = document.getElementById('movie-rating');
  const movieList = document.getElementById('movie-list');
  
  // Ajax parameters
  const xhr = new XMLHttpRequest(),
        method = "POST",
        url = "./ajax/movieFilter.php",
        async = true;
  
  // object array
  let data = {};

  // search for title
  if (option === 'search') {
    // console.log(searchList.value);
    data = {
      "movie-title": searchTitle.value,
      "movie-type": "all",
      "movie-category": "all",
      "movie-rating": "all"
    };
  }

  // show all movies
  if (option === 'onload' || option === 'reset') {
    data = {
      "movie-title": "",
      "movie-type": "all",
      "movie-category": "all",
      "movie-rating": "all"
    };

    // movieTitle.setAttribute('value', '');
    movieType.value = 'all';
    movieCategory.value = 'all';
    movieRating.value = 'all';
    
    // movieTitle.value = '';
    // movieType.value = 'all';
    // movieCategory.value = 'all';
    // movieRating.value = 'all';

  }

  // user defined
  if (option === 'filter') {
    data = {
      "movie-title": "null",
      "movie-type": movieType.value,
      "movie-category": movieCategory.value,
      "movie-rating": movieRating.value
    };  
  }

  // SELECT * FROM `movie_list`
  // ORDER BY `movie_list`. `title` ASC

  // convert data to json string object
  const postData = JSON.stringify(data);
  
  // 
  xhr.open(method, url, async);
  xhr.setRequestHeader("Content-Type", "application/json");
  
  // Handle the response
  xhr.onload = function () {
    
    if (xhr.status === 200) {
      // console.log('Response:', xhr.responseText);
      
      data = JSON.parse(xhr.response);
      let html = '';
      
      if (data.length == 0) {
        html = '<td colspan="4">Sorry! No matches found</td>';
      } else {
        
        data.forEach((elm) => {
          
          html += `<tr>
                    <td>${elm.title}</td>
                    <td>${elm.type}</td>
                    <td>${elm.category}</td>
                    <td>${elm.rating}</td>
                    <td><a href="single-movie.php?id=${elm.id}">View</a></td>
                  </tr>`;
          
          // hmtl = document.createElement('tr');
          // html += `<td>${elm.mov_title}</td>`;
          // html += `<td>${elm.mov_type}</td>`;
          // html += `<td>${elm.mov_category}</td>`;
          // html += `<td>${elm.mov_rating}</td>`;
          // html += `</tr>`;
        });
        
      }

      let tHead = movieList.querySelector('tbody');
      tHead.innerHTML = html;

    } else {
      console.error('Error:', xhr.statusText);
    }
    
  };
  
  // render data array
  xhr.send(postData);

}

// onload 
movieFilter('onload');