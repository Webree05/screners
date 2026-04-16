# Build Stage: Use Python 3.11 as base
FROM python:3.11-slim

# Install System Dependencies & PHP for the Scraper
RUN apt-get update && apt-get install -y \
    php-cli \
    php-curl \
    php-json \
    curl \
    &> /dev/null

# Set Working Directory
WORKDIR /app

# Copy Requirements and Install Python Libraries
COPY backend/requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy the entire project
COPY . .

# Expose the API/Dashboard Port
EXPOSE 8000

# Create a Startup Script to run both Python Server and PHP Scraper
RUN echo '#!/bin/bash\n\
# Start the Python FastAPI Server in the background\n\
python -m uvicorn backend.main:app --host 0.0.0.0 --port 8000 &\n\
\n\
# Start the PHP Scraper loop in the foreground (to keep container alive)\n\
# This ensures data is always being pulled while the server is running\n\
php backend/scraper.php\n\
' > /app/start.sh

RUN chmod +x /app/start.sh

# Run the system
CMD ["/app/start.sh"]
