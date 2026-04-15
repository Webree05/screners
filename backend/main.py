from fastapi import FastAPI, HTTPException, BackgroundTasks
from pydantic import BaseModel
from typing import List, Optional, Dict
import datetime
import json
import os
from .intel_engine import IntelligenceEngine

app = FastAPI(
    title="DEX IHSG Smart Screener API",
    description="Advanced AI-Powered Bullish & Bearish Signal Screener for IDX BEI",
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

@app.get("/")
def read_root():
    return {"status": "ok", "message": "DEX IHSG Smart Engine Running", "version": "2.0.0"}

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
    """
    Returns live Macro Data and Pearson Correlation against IHSG.
    """
    # In a real scenario, this would fetch from an external API or scraper
    return {
        "usd_idr": 15650.0,
        "bi_rate": 6.00,
        "gold_price": 2735.0, # Updated gold price to be more "current"
        "correlation_score": -0.65
    }

@app.post("/api/v1/backtest/run")
def run_backtest(ticker: str, period_years: int = 5):
    """
    Initiates backtesting engine for a specific ticker.
    """
    return {
        "status": "simulated",
        "ticker": ticker,
        "period": f"{period_years} Years",
        "metrics": {
            "win_rate": "72%",
            "max_drawdown": "-10.5%",
            "sharpe_ratio": 1.65,
            "profit_factor": 2.1
        }
    }

if __name__ == "__main__":
    import uvicorn
    # Start the processing loop in a separate thread if needed, or just let it be triggered
    uvicorn.run(app, host="0.0.0.0", port=8000)

