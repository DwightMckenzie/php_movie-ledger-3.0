/**
 * 
 * 
 * 
 * 
 * 
 * 
 */


// display list array
function renderList() {
  "use strict";

  const list = document.querySelector('#list tbody');
  // console.log(list);
  const listArr = Array.from(list.children);
  const paginationElm = document.getElementById('pagination');
  const pageSizeElm = document.getElementById('page-size');
  // list.innerHTML = '';

  console.log(listArr.slice(5,25));

  // let currentPage = 1;
  // let pageSize = 10;
  
  // const start = (currentPage - 1) * pageSize;
  // const end = start + pageSize;

  // let listArr = Array.isArray(list);

  // let test = listArr.slice(start, end);

  // console.log(test);

  // list.slice(start, end).forEach(text => {
  // //   const div = document.createElement('div');
  // //   div.className = 'item';
  // //   div.textContent = text;
  // //   list.appendChild(div);
  //   console.log();
  // });

}

setTimeout(function(){
  renderList();
},2000);
