# Cyber Shield

**Rule-based threat response simulation platform for cybersecurity awareness and controlled attack-response scenarios.**

Cyber Shield analyzes user-provided inputs, classifies common scams and cyber threats using rule-based logic, and provides immediate response guidance. The project also includes a controlled simulation environment for demonstrating attack scenarios and response impact.

## Key capabilities

- Rule-based threat classification
- Security-awareness and threat-response guidance
- Controlled attack simulation
- Modular handling of multiple threat categories
- Practical mitigation recommendations

## Implementation overview

- **Threat knowledge base:** `cybersecurity_help_rules.json` stores the rule-driven security guidance used by the platform.
- **Web application:** HTML pages provide the user-facing awareness and simulation workflows.
- **Server-side components:** PHP endpoints handle account, recovery, history, and application actions.
- **Persistence:** SQL schema support is included for simulator history data.
- **Simulation workflow:** `attack_simulator.html` provides the controlled attack-response demonstration interface.

This structure keeps the threat-classification content, web interface, server-side actions, and simulation history components distinguishable for review and further development.

## Why I built it

The project explores how lightweight, explainable security rules can be used to classify common threats and guide users toward appropriate defensive actions without treating the system as a black-box model.

## Academic recognition

Presented and published at an **IEEE conference**.

## Focus areas

`Threat Detection` `Security Awareness` `Incident Response` `Attack Simulation` `Rule-Based Systems`

## Status

Academic cybersecurity project maintained as part of my security portfolio.
