/* Used to prepolulate "Date" fields with today's date.
   See Citation 1, 2, & 3 below.
*/
function getTDate(){
	var date = new Date();
	var day = date.getDate();
	var month = date.getMonth() + 1;
	var year = date.getFullYear();

	if (month < 10) {month = "0" + month};
	if (day < 10) {day = "0" + day};
	var today = String(year + "-" + month + "-" + day);
	return today;	
}
/* Citations.
[1] https://piazza.com/class/ipg9pgicbxl5mk?cid=129 (Summer 2016 CS-290)
[2] http://stackoverflow.com/questions/6982692/html5-input-type-date-default-value-to-today
[3] http://www.w3schools.com/jsref/jsref_string.asp
*/