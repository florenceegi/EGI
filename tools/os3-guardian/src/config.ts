/**
 * @package OS3Guardian\Config
 * @author Padmin D. Curtis (AI Partner OS3.0)
 * @version 1.0.0 (Padmin Analyzer - Configuration)
 * @date 2025-10-22
 * @purpose Configuration loader per Padmin Analyzer
 */

import * as dotenv from 'dotenv';
import * as path from 'path';
import { RedisConfig } from './db';

// Load .env file
dotenv.config({ path: path.resolve(__dirname, '../.env') });

export interface PadminConfig {
  redis: RedisConfig;
  openai?: {
    apiKey: string;
    model: string;
  };
  anthropic?: {
    apiKey: string;
    model: string;
  };
  workspace: {
    rootPath: string;
    rulesPath: string;
  };
}

export function loadConfig(): PadminConfig {
  const config: PadminConfig = {
    redis: {
      host: process.env.PADMIN_REDIS_HOST || '127.0.0.1',
      port: parseInt(process.env.PADMIN_REDIS_PORT || '6381', 10),
      password: process.env.PADMIN_REDIS_PASSWORD || 'padmin_redis_2025',
      db: parseInt(process.env.PADMIN_REDIS_DB || '0', 10),
    },
    workspace: {
      rootPath: process.env.WORKSPACE_ROOT || path.resolve(__dirname, '../../../'),
      rulesPath:
        process.env.RULES_PATH ||
        path.resolve(__dirname, '../../../.github/copilot-instructions.md'),
    },
  };

  // Optional OpenAI config
  if (process.env.OPENAI_API_KEY) {
    config.openai = {
      apiKey: process.env.OPENAI_API_KEY,
      model: process.env.OPENAI_MODEL || 'gpt-4',
    };
  }

  // Optional Anthropic config
  if (process.env.ANTHROPIC_API_KEY) {
    config.anthropic = {
      apiKey: process.env.ANTHROPIC_API_KEY,
      model: process.env.ANTHROPIC_MODEL || 'claude-3-sonnet-20240229',
    };
  }

  return config;
}

export function validateConfig(config: PadminConfig): boolean {
  if (!config.redis.host || !config.redis.port) {
    console.error('❌ Redis configuration missing');
    return false;
  }

  if (!config.workspace.rootPath) {
    console.error('❌ Workspace root path missing');
    return false;
  }

  return true;
}
