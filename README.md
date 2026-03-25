# 🚀 Smart AIDocs for Laravel

[![Latest Stable Version](https://img.shields.io/github/v/release/enasellithy/SmartAIDocs)](https://github.com/enasellithy/SmartAIDocs)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE.md)

**Smart AIDocs** is an intelligent Laravel package that automates the creation of Markdown documentation and PHPUnit tests for your code using AI (Kimi & Groq). Perfect for developers who want to maintain high-quality docs and testing suites without the manual overhead.

---

## ✨ Features
* **AI Documentation:** Generates clear, structured Markdown files for your classes.
* **Smart Unit Testing:** Auto-generates PHPUnit tests using modern Laravel syntax.
* **Dual Provider Support:** Seamlessly switch between **Kimi AI** and **Groq Cloud**.
* **Automatic Fallback:** Intelligent error handling if an AI provider is unavailable.

---

## 🛠 Installation

### 1. Add Repository
Since the package is currently in development, add the GitHub repository to your project's `composer.json`:

```bash

composer config repositories.smart_ai_docs vcs https://github.com/enasellithy/SmartAIDocs
composer require enasellithy/smart-ai-docs:dev-master --no-audit


```

### 2. KIMI_API_KEY=your_kimi_api_key_here
KIMI_API_KEY=your_kimi_api_key_here
GROQ_API_KEY=your_groq_api_key_here

### 3. Publish Configuration

```bash
php artisan vendor:publish --provider="SmartAIDocs\SmartAIDocsServiceProvider"
```

### Emample
Generate Documentation only

```bash

php artisan ai:generate app/Models/User.php --docs
php artisan ai:generate app/Http/Controllers/UserController.php --test
php artisan ai:generate app/Services/PaymentService.php --docs --test
php artisan ai:generate app/Http/Controllers --docs --test
```

### Ollama Support (Local AI)

```bash

ollama pull deepseek-coder:6.7b
ollama pull qwen2.5-coder:1.5b

```

```bash 

composer require enasellithy/smart-ai-docs:v1.1.0

```


### Configuration

in env 
SMART_AI_DEFAULT=ollama
OLLAMA_BASE_URL=http://localhost:11434