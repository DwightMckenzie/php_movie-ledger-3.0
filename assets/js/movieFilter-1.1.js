/**
 * Render results to the page
 * 
 * 
 */
function movieRowCount() {
	const movieList = document.getElementById('movie-list');
	const movieRow = movieList.querySelector('tbody');
	const movieResults = movieRow.children.length;
	const resultsFound = document.querySelector('#results-found span');

	resultsFound.innerHTML = movieResults;

	// console.log();

}

let currentPage = 0;
let pageSizeValue = 0;
const paginationElm = document.getElementById('pagination');
const pageSizeElm = document.getElementById('page-size');

// create and display pagination
function renderPagination(data) {

	// clear current pagination html content
	paginationElm.innerHTML = '';

	// get the page count
	const pageCount = Math.ceil(data.length / pageSize);

	// create previous button
	const prevBtn = document.createElement('li');
	prevBtn.setAttribute('class', 'page-item');
	prevBtn.innerHTML = '<a class="page-link" href="#">Previous</a>';
	prevBtn.disabled = currentPage === 1;
	prevBtn.onclick = () => {
		currentPage--;
		movieFilter('prev');
	};

	// add the previous button 
	paginationElm.appendChild(prevBtn);


	/**
	* loop through the total of the pages
	* create index list for pagination
	*/
	for (let i = 1; i <= pageCount; i++) {
		// create a button
		const btn = document.createElement('li');
		btn.setAttribute('class', 'page-item');

		// print current index in button
		btn.innerHTML = `<a class="page-link" href="#">${i}</a>`;

		// set current page
		if (i === currentPage) {
		  btn.classList.add('active');
		}

		// add click function to each button
		btn.onclick = () => {
		  currentPage = i;      
		  movieFilter('direct');
		};

		// repeat 
		paginationElm.appendChild(btn);
	}


	// create next button
	const nextBtn = document.createElement('li');
	nextBtn.setAttribute('class', 'page-item');
	nextBtn.innerHTML = '<a class="page-link" href="#">Next</a>';
	nextBtn.disabled = currentPage === pageCount;
	nextBtn.onclick = () => {
		currentPage++;
		movieFilter('next');
	};
	
	
	// add the next button and close
	paginationElm.appendChild(nextBtn);

}

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
	const movieOrder = document.getElementById('movie-order');
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
			"movie-order": "uo",
			"movie-title": searchTitle.value,
			"movie-type": "all",
			"movie-category": "all",
			"movie-rating": "all"
		};
	}

	// show all movies
	if (option === 'onload' || option === 'reset' || option === undefined) {
		data = {
			"movie-order": "uo",
			"movie-title": "null",
			"movie-type": "all",
			"movie-category": "all",
			"movie-rating": "all"
		};

		// movieTitle.setAttribute('value', '');
		movieOrder.value = 'uo';
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
			"movie-order": "uo",
			"movie-title": "null",
			"movie-type": movieType.value,
			"movie-category": movieCategory.value,
			"movie-rating": movieRating.value
		};

		// reset to index 1
		currentPage = 1;

	}

	// sort by order
	if (option === 'order') {
		data = {
			"movie-order": movieOrder.value,
			"movie-title": "null",
			"movie-type": movieType.value,
			"movie-category": movieCategory.value,
			"movie-rating": movieRating.value
		}
	}

	// for pagination
	if (option === 'page-size') {
		data = {
			"movie-order": 'asc',
			"movie-title": "null",
			"movie-type": movieType.value,
			"movie-category": movieCategory.value,
			"movie-rating": movieRating.value
		}

		// reset to index 1
		currentPage = 1;

	}

	// pagination controls
	if (option === 'prev' || option === 'direct' || option === 'next') {
		data = {
			"movie-order": movieOrder.value,
			"movie-title": "null",
			"movie-type": movieType.value,
			"movie-category": movieCategory.value,
			"movie-rating": movieRating.value
		}
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
				html = '<td colspan="4" class="table-warning">Sorry! No matches found</td>';
			} else {

				// let currentPage = 1;
				// let pageSize = 10;

				// const list = document.querySelector('#list tbody');
				// console.log(list);
				// const listArr = Array.from(list.children);
				// const paginationElm = document.getElementById('pagination');

				// const pageSizeElm = document.getElementById('page-size');

				pageSizeValue = parseInt(pageSizeElm.value, 10);
				// pageSizeElm.value = "null" ? pageSize = -1 : parseInt(pageSizeElm.value, 10);

				// brought back in.
				const start = currentPage * pageSizeValue;
				const end = start + pageSizeValue;


				// MY ATTEMPT
				// let start = (currentPage - 1) * pageSize - 1;
				// let end = 0;
				
				// if (pageSize >= -1) {
					// // start = (start + pageSize) + pageSize;
					// end = pageSize;
				// } else {
					// end = data.length;
					// // const end = start + pageSize + 1;					
				// }
				
				
				// 
				renderPagination(data);

				// 
				data.slice(start, end).forEach((elm) => {

					html += `<tr>
					<td>${elm.title}</td>
					<td>${elm.type}</td>
					<td>${elm.category}</td>
					<td>${elm.rating}</td>
					<td><a href="single-movie.php?id=${elm.id}"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
					</tr>`;
				});

				// ORIGINAL
				// data.forEach((elm) => {

				//   html += `<tr>
				//             <td>${elm.title}</td>
				//             <td>${elm.type}</td>
				//             <td>${elm.category}</td>
				//             <td>${elm.rating}</td>
				//             <td><a href="single-movie.php?id=${elm.id}">View</a></td>
				//           </tr>`;

				//   // hmtl = document.createElement('tr');
				//   // html += `<td>${elm.mov_title}</td>`;
				//   // html += `<td>${elm.mov_type}</td>`;
				//   // html += `<td>${elm.mov_category}</td>`;
				//   // html += `<td>${elm.mov_rating}</td>`;
				//   // html += `</tr>`;
				// });
			
			}

			let tHead = movieList.querySelector('tbody');
			tHead.innerHTML = html;

			// trigger row count
			movieRowCount();

		} else {
			console.error('Error:', xhr.statusText);
		}

	};
  
	// render data array
	xhr.send(postData);

}

// function listSize() {
//   const pageSizeElm = document.getElementById('page-size');
//   pageSize = parseInt(pageSizeElm.value, 10);
//   currentPage = 1;
//   movieFilter();
// }

// onload 
movieFilter('onload');