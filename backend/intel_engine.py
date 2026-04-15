import pandas as pd
import numpy as np
import json
import os
import datetime
from sklearn.preprocessing import MinMaxScaler

class IntelligenceEngine:
    """
    Advanced & Super Smart Data Processing Engine for IDX Screener.
    Incorporates statistical modeling, trend analysis, and pattern recognition.
    """
    
    def __init__(self, data_path="../market_data.json"):
        self.data_path = os.path.join(os.path.dirname(__file__), data_path)
        self.market_data = {}
        self.processed_data = {}

    def load_data(self):
        try:
            if os.path.exists(self.data_path):
                with open(self.data_path, 'r') as f:
                    self.market_data = json.load(f)
                return True
        except Exception as e:
            print(f"Error loading market data: {e}")
        return False

    def calculate_technical_indicators(self, prices, volumes):
        if len(prices) < 2:
            return {}
        
        df = pd.DataFrame({'close': prices, 'volume': volumes})
        
        # 1. RSI (Relative Strength Index) - Smart Momentum
        delta = df['close'].diff()
        gain = (delta.where(delta > 0, 0)).rolling(window=14).mean()
        loss = (-delta.where(delta < 0, 0)).rolling(window=14).mean()
        rs = gain / loss
        df['rsi'] = 100 - (100 / (1 + rs))
        
        # 2. EMA (Exponential Moving Average) - Smart Trend
        df['ema20'] = df['close'].ewm(span=20, adjust=False).mean()
        df['ema50'] = df['close'].ewm(span=50, adjust=False).mean()
        
        # 3. Bollinger Bands - Smart Volatility
        df['ma20'] = df['close'].rolling(window=20).mean()
        df['std20'] = df['close'].rolling(window=20).std()
        df['upper_band'] = df['ma20'] + (df['std20'] * 2)
        df['lower_band'] = df['ma20'] - (df['std20'] * 2)
        
        # 4. Volume Trend (OBV - On Balance Volume)
        df['obv'] = (np.sign(df['close'].diff()) * df['volume']).fillna(0).cumsum()
        
        return df

    def calculate_smart_score(self, ticker, ticker_data):
        """
        Combines traditional formulas with advanced AI logic.
        Preserves original rules but enhances them with statistical confidence.
        """
        try:
            timestamps = ticker_data.get('timestamp', [])
            closes = ticker_data.get('close', [])
            volumes = ticker_data.get('volume', [])
            
            # Filter nulls
            valid_indices = [i for i, c in enumerate(closes) if c is not None]
            if len(valid_indices) < 20:
                return None
            
            clean_closes = [closes[i] for i in valid_indices]
            clean_volumes = [volumes[i] for i in valid_indices if i < len(volumes) and volumes[i] is not None]
            
            if len(clean_closes) < 20: return None
            
            df = self.calculate_technical_indicators(clean_closes, clean_volumes)
            last_row = df.iloc[-1]
            prev_row = df.iloc[-2] if len(df) > 1 else last_row
            
            # --- Traditional Formula Component (Preserved) ---
            # Score = (0.35 * M) + (0.35 * (-Z)) + (0.20 * VP) - (0.10 * sigma_vol)
            
            pt = clean_closes[-1]
            pt_n = clean_closes[-5] if len(clean_closes) >= 5 else clean_closes[0]
            mu = df['ma20'].iloc[-1] if not pd.isna(df['ma20'].iloc[-1]) else pt
            sigma = df['std20'].iloc[-1] if not pd.isna(df['std20'].iloc[-1]) and df['std20'].iloc[-1] != 0 else 1
            vt = clean_volumes[-1] if clean_volumes else 0
            vavg = sum(clean_volumes[-20:]) / len(clean_volumes[-20:]) if clean_volumes else 1
            pt_prev = clean_closes[-2] if len(clean_closes) >= 2 else pt
            
            M = (pt - pt_n) / pt_n if pt_n != 0 else 0
            Z = (pt - mu) / sigma
            VP = vt / vavg if vavg != 0 else 0
            r = np.log(pt / pt_prev) if pt_prev != 0 and (pt/pt_prev) > 0 else 0
            sigma_vol = abs(r)
            
            traditional_score = (0.35 * M) + (0.35 * (-Z)) + (0.20 * VP) - (0.10 * sigma_vol)
            
            # --- Smart AI Enhancement ---
            # Trend Strength (EMA Crossover)
            trend_strength = 1.0 if last_row['ema20'] > last_row['ema50'] else -1.0
            
            # Oversold/Overbought (RSI)
            rsi_factor = 0
            if last_row['rsi'] < 30: rsi_factor = 0.5  # Strong Bullish Oversold
            elif last_row['rsi'] > 70: rsi_factor = -0.5 # Strong Bearish Overbought
            
            # Volatility Breakout
            breakout_factor = 0
            if pt > last_row['upper_band']: breakout_factor = 0.3
            elif pt < last_row['lower_band']: breakout_factor = -0.3
            
            # Final Smart Score (Blended)
            ai_score = (traditional_score * 0.6) + (trend_strength * 0.2) + (rsi_factor * 0.1) + (breakout_factor * 0.1)
            
            return {
                "ticker": ticker,
                "score": float(ai_score),
                "traditional_score": float(traditional_score),
                "signal": "BUY" if ai_score > 0.4 else "SELL" if ai_score < -0.4 else "HOLD",
                "indicators": {
                    "rsi": float(last_row['rsi']) if not pd.isna(last_row['rsi']) else 50,
                    "ema_trend": "Bullish" if trend_strength > 0 else "Bearish",
                    "volatility": "High" if sigma_vol > 0.02 else "Normal",
                    "momentum": float(M)
                },
                "last_update": datetime.datetime.now().isoformat()
            }
        except Exception as e:
            print(f"Error calculating score for {ticker}: {e}")
            return None

    def process_all(self):
        if not self.load_data(): return {}
        
        results = {}
        for ticker, data in self.market_data.items():
            score_data = self.calculate_smart_score(ticker, data)
            if score_data:
                results[ticker] = score_data
        
        self.processed_data = results
        return results

    def save_intelligence(self, output_path="../intelligence_report.json"):
        output_path = os.path.join(os.path.dirname(__file__), output_path)
        try:
            with open(output_path, 'w') as f:
                json.dump(self.processed_data, f, indent=4)
            return True
        except Exception as e:
            print(f"Error saving intelligence report: {e}")
        return False

if __name__ == "__main__":
    engine = IntelligenceEngine()
    print("Starting Advanced Intelligence Engine...")
    results = engine.process_all()
    print(f"Processed {len(results)} tickers.")
    if engine.save_intelligence():
        print("Intelligence report saved successfully.")
