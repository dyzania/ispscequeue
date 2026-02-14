from fastapi import FastAPI
from pydantic import BaseModel
from transformers import pipeline

app = FastAPI(title="Sentiment Analysis API")

# -------------------------------------------------------------------------
# PRODUCTION HOSTING:s
# 1. Install dependencies: pip install fastapi uvicorn transformers torch
# 2. Run with a production server: uvicorn app:app --host 0.0.0.0 --port 8000
# 3. If using a firewall, ensure port 8000 is open for your PHP server's IP.
# ------------------------------------P-------------------------------------

model_id = "tabularisai/multilingual-sentiment-analysis"

sentiment_pipeline = pipeline(
    "text-classification",
    model=model_id,
    tokenizer=model_id,
    truncation=True,
    top_k=1,          # return only top label
    return_all_scores=False
)

class TextInput(BaseModel):
    text: str

@app.post("/analyze")
def analyze_sentiment(data: TextInput):
    try:
        result = sentiment_pipeline(data.text)
        if isinstance(result, list) and len(result) > 0:
            if isinstance(result[0], list):
                result = result[0][0]
            else:
                result = result[0]
        return {
            "label": result["label"],
            "score": round(result["score"], 4)
        }
    except Exception as e:
        return {"error": str(e)}
