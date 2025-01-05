function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}

function filterFunction() {
  var input, filter, a, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  div = document.getElementById("myDropdown");
  a = div.getElementsByTagName("a");
  for (i = 0; i < a.length; i++) {
    txtValue = a[i].textContent || a[i].innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      a[i].style.display = "";
    } else {
      a[i].style.display = "none";
    }
  }
}

function selectOrg(vl, id) {
  $("#organizationdropdown").val(vl);
  $("#myDropdown").removeClass("show");
  $("#organization").val(id);
}

function selectOrg1(vl, id) {
  $("#organizationdropdown1").val(vl);
  $("#myDropdown").removeClass("show");
  $("#tokenlist__search").val(id);
}

function selectName(vl, id) {
  $("#organizationdropdown").val(vl);
  $("#myDropdown").removeClass("show");
  $("#organization").val(id);
}