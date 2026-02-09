from openai import OpenAI
import os
from pathlib import Path
import requests

BASE_DIR = Path(__file__).parent
AGENT_PATH = BASE_DIR / ".github" / "agents" / "Programista.agent.md"
PROMPT_PATH = BASE_DIR / ".github" / "prompts" / "Prompt.prompt.md"
DEV_DOCS_URL = "https://devdocs.prestashop-project.org/8/modules/creation/"

client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))

def fetch_devdocs() -> str:
    resp = requests.get(DEV_DOCS_URL, timeout=15)
    resp.raise_for_status()
    print("Pobrano devdocs:", DEV_DOCS_URL, "status:", resp.status_code)
    return resp.text[:4000]  # skrót do promptu

def run_agent(user_input: str) -> str:
    system_prompt = AGENT_PATH.read_text(encoding="utf-8")
    project_prompt = PROMPT_PATH.read_text(encoding="utf-8")
    devdocs_snippet = fetch_devdocs()

    response = client.chat.completions.create(
        model="gpt-5.1-codex",
        messages=[
            {"role": "system", "content": system_prompt},
            {"role": "system", "content": project_prompt},
            {"role": "system", "content": f"DevDocs (skrót):\n{devdocs_snippet}"},
            {"role": "user", "content": user_input},
        ],
    )
    return response.choices[0].message.content

if __name__ == "__main__":
    while True:
        user_input = input("Podaj swoje pytanie do agenta (exit aby wyjść): ")
        if user_input.strip().lower() in {"exit", "quit"}:
            break
        print(run_agent(user_input))