class LibraryStorage {
  constructor() {
    this.FILE_PATH = 'data/borrow_records.json';
  }

  async loadRecords() {
    try {
      const response = await fetch(this.FILE_PATH);
      if (response.ok) {
        return await response.json();
      }
      return [];
    } catch (e) {
      console.error('無法讀取借閱記錄:', e);
      return [];
    }
  }

  async saveRecords(records) {
    try {
      const response = await fetch('save_borrow.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(records)
      });
      
      if (!response.ok) {
        throw new Error('儲存失敗');
      }
      
      // 更新本地儲存
      localStorage.setItem('borrowedRecords', JSON.stringify(records));
      
    } catch (e) {
      console.error('儲存借閱記錄失敗:', e);
      alert('儲存借閱記錄失敗，請稍後再試');
    }
  }
}