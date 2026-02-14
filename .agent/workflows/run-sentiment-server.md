---
description: How to run the local Python Sentiment Analysis server
---

To run the sentiment analysis server locally, follow these steps:

1. Open a **new terminal** (don't close the one running your website).
2. Navigate to the sentiment analysis directory:
   ```powershell
   cd c:\xampp\htdocs\equeue-system\sentiment_analysis
   ```
3. Activate the virtual environment:

   **Option A: PowerShell (If scripts are disabled)**

   ```powershell
   cd c:\xampp\htdocs\equeue-system
   Set-ExecutionPolicy -ExecutionPolicy Bypass -Scope Process
   .\venv\Scripts\Activate.ps1
   ```

   **Option B: Command Prompt (Recommended if PowerShell fails)**

   ```cmd
   cd c:\xampp\htdocs\equeue-system
   .\venv\Scripts\activate.bat
   ```

4. Start the server using Uvicorn:
   ```powershell
   cd sentiment_analysis
   uvicorn app:app --host 127.0.0.1 --port 8000
   ```

**Fixing "Invalid application" or "Failed to run" errors:**

It looks like the `venv` folder might be corrupted or was created for a different system. Follow these steps to fix it:

1. **Navigate to root** (if not already there):
   ```powershell
   cd c:\xampp\htdocs\equeue-system
   ```
2. **Delete the old folder**:
   ```powershell
   Remove-Item -Path .\venv -Recurse -Force
   ```
3. **Recreate the environment**:
   ```powershell
   python -m venv venv
   ```
4. **Activate and Install Dependencies**:
   ```powershell
   .\venv\Scripts\Activate.ps1
   pip install fastapi uvicorn transformers torch pydantic
   ```
5. **Try running again**:
   ```powershell
   cd sentiment_analysis
   uvicorn app:app --port 8000
   ```
