# -*- coding: utf-8 -*-
import streamlit as st
import pandas as pd
import math

st.set_page_config(page_title="海巡署圖書查詢系統", layout="wide")

# 讀取資料，明確指定 UTF-8 編碼
@st.cache_data
def load_data():
    thesis = pd.read_csv('thesis.csv', encoding='utf-8')
    journal = pd.read_csv('journal.csv', encoding='utf-8')
    book = pd.read_csv('books.csv', encoding='utf-8')
    return thesis, journal, book

thesis, journal, book = load_data()

st.markdown("""
<style>
@media (max-width: 430px) {
    .block-container { padding: 0.5rem 0.2rem !important; }
    .stDataFrame { font-size: 0.92rem; }
    .stTextInput>div>div>input { font-size: 1rem; }
}
</style>
""", unsafe_allow_html=True)

st.title("海巡署教育訓練測考中心圖書查詢系統")

tab = st.tabs(["論文查詢", "期刊查詢", "書籍查詢"])

PAGE_SIZE = 30

def show_table(df, search_cols, tab_key):
    cols = st.columns(len(search_cols))
    query = {}
    for i, col in enumerate(search_cols):
        with cols[i]:
            val = st.text_input(f"請輸入{col}關鍵字", key=f"{tab_key}_{col}")
            query[col] = val.strip()
    filtered = df
    for col, val in query.items():
        if val:
            filtered = filtered[filtered[col].astype(str).str.contains(val, case=False, na=False)]
    total = len(filtered)
    total_pages = max(1, math.ceil(total / PAGE_SIZE))
    page = st.number_input("頁數", min_value=1, max_value=total_pages, value=1, step=1, key=f"{tab_key}_page")
    start = (page-1)*PAGE_SIZE
    end = start+PAGE_SIZE
    st.dataframe(filtered.iloc[start:end], use_container_width=True)
    st.caption(f"共 {total} 筆，頁數 {page}/{total_pages}")

with tab[0]:
    show_table(
        thesis,
        ["論文名稱", "研究生", "指導教授"],
        "thesis"
    )

with tab[1]:
    show_table(
        journal,
        ["刊名", "期數", "出版者"],
        "journal"
    )

with tab[2]:
    show_table(
        book,
        ["書名", "作者", "分類"],
        "book"
    )
