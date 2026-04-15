from fastapi import FastAPI, HTTPException, BackgroundTasks, Request
from fastapi.responses import FileResponse, Response
from fastapi.staticfiles import StaticFiles
from pydantic import BaseModel
from typing import List, Optional, Dict
import datetime
import json
import os
import httpx
from .intel_engine import IntelligenceEngine

app = FastAPI(
    title="DEX IHSG Smart Screener API & Server",
    description="Advanced AI-Powered Bullish & Bearish Signal Screener with Integrated Web Server",
    version="2.0.0"
)

# Initialize Engine
engine = IntelligenceEngine()

# --- MODELS ---

class SignalResponse(BaseModel):
    ticker: str
    score: float
    traditional_score: float
    signal: str
    indicators: Dict[str, object]
    last_update: str

class MacroData(BaseModel):
    usd_idr: float
    bi_rate: float
    gold_price: float
    correlation_score: float

# --- ENDPOINTS ---

@app.get("/api/v1/intel/process")
def process_intel():
    """
    Manually triggers the Intelligence Engine to process the latest market data.
    """
    results = engine.process_all()
    engine.save_intelligence()
    return {"status": "success", "processed_count": len(results)}

@app.get("/api/v1/signals/all", response_model=Dict[str, SignalResponse])
def get_all_signals():
    """
    Returns all processed signals from the intelligence report.
    """
    report_path = os.path.join(os.path.dirname(__file__), "../intelligence_report.json")
    if os.path.exists(report_path):
        with open(report_path, 'r') as f:
            return json.load(f)
    return {}

@app.get("/api/v1/signals/{ticker}", response_model=SignalResponse)
def get_ticker_signal(ticker: str):
    """
    Returns smart signal for a specific ticker.
    """
    ticker = ticker.upper()
    report_path = os.path.join(os.path.dirname(__file__), "../intelligence_report.json")
    if os.path.exists(report_path):
        with open(report_path, 'r') as f:
            data = json.load(f)
            if ticker in data:
                return data[ticker]
    
    # If not in report, try processing live (expensive)
    engine.load_data()
    if ticker in engine.market_data:
        res = engine.calculate_smart_score(ticker, engine.market_data[ticker])
        if res: return res
        
    throw_msg = f"Ticker {ticker} not found or insufficient data."
    raise HTTPException(status_code=404, detail=throw_msg)

@app.get("/api/v1/macros", response_model=MacroData)
def get_macro_correlation():
    return {
        "usd_idr": 15650.0,
        "bi_rate": 6.00,
        "gold_price": 2735.0,
        "correlation_score": -0.65
    }

# --- UNIVERSAL PROXY (Replaces proxy.php) ---
@app.get("/proxy.php")
@app.get("/proxy")
async def universal_proxy(url: str):
    """
    High-performance proxy to bypass CORS for market data APIs.
    Compatible with frontend calls to proxy.php.
    """
    try:
        async with httpx.AsyncClient() as client:
            # Add common Headers to look like a browser
            headers = {
                "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36"
            }
            resp = await client.get(url, headers=headers, timeout=10)
            return Response(content=resp.content, media_type=resp.headers.get("Content-Type"))
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))

# --- STATIC FILES & DASHBOARD ---
# Mount static files from the project root (index.html, etc.)
# Note: In production, you'd want to be more specific, but for this "Integrated Engine" 
# we want to serve everything from the root folder.
ROOT_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))

@app.get("/")
async def serve_dashboard():
    return FileResponse(os.path.join(ROOT_DIR, "index.html"))

# Mount the entire directory for assets, scripts, and JSON data
app.mount("/", StaticFiles(directory=ROOT_DIR), name="root")

if __name__ == "__main__":
    import uvicorn
    # Python Server starts on port 8000
    # Access: http://localhost:8000
    uvicorn.run(app, host="0.0.0.0", port=8000)

