import streamlit as st

st.set_page_config(page_title="嵌入外部網頁", layout="wide")

st.title("嵌入外部網頁展示")

st.components.v1.iframe("https://shang0320.github.io/", height=700)
