/* Used to prepolulate "Date" fields with today's date. */
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
