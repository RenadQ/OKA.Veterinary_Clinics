let currentBranch = "";

// Function to show the selected tab and load its data
function showTab(tabName, branchName) {
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => tab.classList.remove('active'));

    const tabElements = document.querySelectorAll('.tab');
    tabElements.forEach(tab => tab.classList.remove('active'));

    currentBranch = branchName;
    document.getElementById(tabName).classList.add('active');

    if (tabName === 'availableItemsTab') {
        fetchItems(branchName, 'available');
    } else if (tabName === 'outOfStockTab') {
        fetchItems(branchName, 'outOfStock');
    }
}

// Function to fetch items from the server
function fetchItems(branchName, status) {
    fetch(`productmanagement.php?branch=${branchName}&status=${status}`)
        .then(response => response.json())
        .then(data => {
            const listId = status === 'available' ? 'availableItemsList' : 'outOfStockList';
            const list = document.getElementById(listId);
            list.innerHTML = data.map(item => `<li>${item.name || item}</li>`).join('');
        });
}