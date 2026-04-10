# DEX IHSG Screener

AI-Powered Bullish & Bearish Signal Screener for IHSG BEI. This project contains the Frontend Dashboard Prototype (React + Tailwind + Lightweight Charts) and the Backend Boilerplate Engine (FastAPI).

## Project Structure

```text
c:\laragon\www\screners\
├── index.html        # Frontend Main entry point
├── app.jsx           # Frontend React UI & Logic
└── backend/
    ├── requirements.txt # Python dependencies
    └── main.py          # FastAPI Application Engine
```

## Running the Application

### Frontend (Instant Prototype)
Since this directory is hosted on **Laragon**, you can instantly view the highly interactive UI prototype bypassing Node.js setup:
1. Open your browser and navigate to exactly where your local server maps: `http://localhost/screners/` or `http://screners.test/`
2. The UI is built using React, Babel, and Tailwind CSS compiled directly in the browser via CDN for immediate prototyping.

### Backend (FastAPI Engine)
The backend manages AI model scoring and interactions. To initialize it, you need Python.

1. Create a virtual environment:
```bash
cd backend
python -m venv venv
.\venv\Scripts\activate
```

2. Install dependencies (TensorFlow, Scikit-learn, etc):
```bash
pip install -r requirements.txt
```

3. Run the FastAPI development server:
```bash
uvicorn main:app --reload
```
You can access the backend swagger UI at `http://127.0.0.1:8000/docs`.

---

## Technical Mapping to User Requirements

### 1. Frontend & Visuals
- **Frameworks Used**: React.js with TailwindCSS (for precise styling/Dark Mode), Lightweight Charts for rendering stock trends.
- **Aesthetics**: Premium Dark mode (`#0F172A`) with Cyan (`#00F0FF`) and Purple (`#6C5CE7`) gradients. Glassmorphism panels, interactive SVG icons from Lucide, and neon borders simulate a professional trading deck.

### 2. Core Dashboard Features Mapped
- **Smart Money Tracker**: Shows foreign flow tracking with top broker Net Volume charts (ex: BBCA Net Buy). logic implementation visualizer.
- **AI Sentiment Aggregator**: Circular loading bar scoring model output based on simulated News & Social scraping (NLP Transformer).
- **Macro Correlation Dashboard**: USD/IDR inversion tracking and Gold/BI Rate comparisons.
- **Technical Chart**: TradingView Lightweight chart mapping BBCA candlestick + EMA 50 trend smoothing overlay.

### 3. Backend Models & Prediction Logic
- The **FastAPI** (`backend/main.py`) framework handles the AI scoring models (LSTM and Scikit-learn outputs simulation via REST) and interfaces for trading algorithms (Squeeze Algorithm, EMA Ribbon).
- The `SignalResponse` model handles the precise "High Probability Bullish Signal" outputs matching the *Perfect Signal Logic* rules.
- **Backtesting & Telegram integration** modules have stubbed endpoints that map directly to the 5-years historical IHSG processor and `python-telegram-bot` broadcasts.
