const pagination = new Pagination(50); // 每頁50筆

function render(type) {
  const body = document.getElementById(`${type}-body`);
  body.innerHTML = '';
  
  // 獲取分頁後的資料
  const pageData = pagination.paginate(dataStore[type]);
  
  pageData.forEach((r, index) => {
    // ... 原有的渲染邏輯 ...
  });
  
  // 渲染分頁控制項
  pagination.renderPagination(
    dataStore[type].length,
    `${type}-pagination`
  );
}

// 設定分頁變更時的回調
pagination.setOnChange(() => {
  const activeTab = document.querySelector('.tab-content:not(.d-none)').id;
  render(activeTab);
});