






const currencySelect = document.getElementById('currencySelect');
const dataLines = csvData.trim().split('\n');
const headers = dataLines.shift().split(',');

dataLines.forEach(line => {
  const [country, currencyCode] = line.split(',');
  const option = document.createElement('option');
  option.value = currencyCode;
  option.textContent = `${country} (${currencyCode})`;
  currencySelect.appendChild(option);
});

function validateForm() {
  const form = document.getElementById("reg"); // Change this ID to match your form's ID

  // Get all input and select elements within the form
  const formElements = form.querySelectorAll("input, select");

  // Flag to track if any field is empty
  let isValid = true;

  // Loop through each element and check if it's empty
  for (const element of formElements) {
      if (element.value.trim() === "") {
          isValid = false;
          // Add a red border or some visual indicator to highlight empty fields
          element.style.border = "1.5px solid red";
      } else {
          // Reset the style if the field is not empty
          element.style.border = "";
      }
  }

  if (!isValid) {
      
  }

  return isValid;
}
