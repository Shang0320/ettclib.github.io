class Pagination {
  constructor(pageSize = 50) {
    this.pageSize = pageSize;
    this.currentPage = 1;
  }

  paginate(data) {
    const start = (this.currentPage - 1) * this.pageSize;
    const end = start + this.pageSize;
    return data.slice(start, end);
  }

  getTotalPages(totalItems) {
    return Math.ceil(totalItems / this.pageSize);
  }

  renderPagination(totalItems, containerId) {
    const totalPages = this.getTotalPages(totalItems);
    const container = document.getElementById(containerId);
    container.innerHTML = '';

    const nav = document.createElement('nav');
    nav.className = 'mt-3';
    const ul = document.createElement('ul');
    ul.className = 'pagination justify-content-center';

    // Previous button
    ul.appendChild(this.createPageItem('上一頁', this.currentPage > 1, () => {
      this.currentPage--;
      this.onChange();
    }));

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
      ul.appendChild(this.createPageItem(i, true, () => {
        this.currentPage = i;
        this.onChange();
      }, i === this.currentPage));
    }

    // Next button
    ul.appendChild(this.createPageItem('下一頁', this.currentPage < totalPages, () => {
      this.currentPage++;
      this.onChange();
    }));

    nav.appendChild(ul);
    container.appendChild(nav);
  }

  createPageItem(text, enabled, onClick, active = false) {
    const li = document.createElement('li');
    li.className = `page-item ${!enabled ? 'disabled' : ''} ${active ? 'active' : ''}`;
    
    const a = document.createElement('a');
    a.className = 'page-link';
    a.href = '#';
    a.textContent = text;
    
    if (enabled) {
      a.addEventListener('click', (e) => {
        e.preventDefault();
        onClick();
      });
    }

    li.appendChild(a);
    return li;
  }

  setOnChange(callback) {
    this.onChange = callback;
  }
}

// 在每個表格下方加入分頁容器
<div id="thesis-pagination"></div>
<div id="journal-pagination"></div>
<div id="book-pagination"></div>